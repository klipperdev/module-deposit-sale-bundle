<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Module\DepositSaleBundle\Doctrine\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Klipper\Component\CodeGenerator\CodeGenerator;
use Klipper\Component\DoctrineChoice\ChoiceManagerInterface;
use Klipper\Component\DoctrineExtensionsExtra\Util\ListenerUtil;
use Klipper\Component\DoctrineExtra\Util\ClassUtils;
use Klipper\Module\DepositSaleBundle\Model\DepositSaleInterface;
use Klipper\Module\DepositSaleBundle\Model\Traits\DepositSaleModuleableInterface;
use Klipper\Module\DepositSaleBundle\Model\Traits\DeviceDepositSaleableInterface;
use Klipper\Module\DeviceBundle\Model\DeviceInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class DepositSaleSubscriber implements EventSubscriber
{
    private ChoiceManagerInterface $choiceManager;

    private CodeGenerator $generator;

    private TranslatorInterface $translator;

    private array $closedStatues;

    private array $availableStatues;

    public function __construct(
        ChoiceManagerInterface $choiceManager,
        CodeGenerator $generator,
        TranslatorInterface $translator,
        array $closedStatues = [],
        array $availableStatues = []
    ) {
        $this->choiceManager = $choiceManager;
        $this->generator = $generator;
        $this->translator = $translator;
        $this->closedStatues = $closedStatues;
        $this->availableStatues = $availableStatues;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::onFlush,
        ];
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->preUpdate($event);
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $object = $event->getObject();

        if ($object instanceof DepositSaleInterface) {
            if (null === $object->getReference()) {
                $object->setReference($this->generator->generate());
            }
        }
    }

    public function onFlush(OnFlushEventArgs $event): void
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $object) {
            $this->updateLastDepositSaleOnDevice($em, $object, true);
            $this->updateProduct($em, $object, true);
            $this->updateAccount($em, $object);
            $this->updateStatus($object, true);
            $this->updateReceiptedAt($em, $object, true);
            $this->updateClosed($em, $object, true);
            $this->updateAvailable($em, $object, true);
            $this->validateDevice($object);
            $this->updateDevice($em, $object);
            $this->updateDeviceStatus($em, $object, true);
        }

        foreach ($uow->getScheduledEntityUpdates() as $object) {
            $this->updateLastDepositSaleOnDevice($em, $object);
            $this->updateProduct($em, $object);
            $this->updateAccount($em, $object);
            $this->updateStatus($object);
            $this->updateReceiptedAt($em, $object);
            $this->updateClosed($em, $object);
            $this->updateAvailable($em, $object);
            $this->validateDevice($object);
            $this->updateDevice($em, $object);
            $this->updateDeviceStatus($em, $object);
        }
    }

    private function updateStatus(object $object, bool $create = false): void
    {
        if ($object instanceof DepositSaleInterface) {
            if ($create) {
                if (null === $object->getStatus()) {
                    $account = $object->getAccount();
                    $depositSaleStatus = null;

                    if ($account instanceof DepositSaleModuleableInterface && null !== ($module = $account->getDepositSaleModule())) {
                        $depositSaleStatus = $depositSaleStatus ?? $module->getDefaultStatus();
                    }

                    $depositSaleStatus = $depositSaleStatus ?? $this->choiceManager->getChoice('deposit_sale_status', null);

                    if (null !== $depositSaleStatus) {
                        $object->setStatus($depositSaleStatus);
                    }
                }
            }
        }
    }

    private function updateLastDepositSaleOnDevice(EntityManagerInterface $em, object $object, bool $create = false): void
    {
        if ($object instanceof DepositSaleInterface && null !== $device = $object->getDevice()) {
            if ($device instanceof DeviceDepositSaleableInterface) {
                $uow = $em->getUnitOfWork();

                if ($create && null !== $device->getLastDepositSale() && !$device->getLastDepositSale()->isClosed()) {
                    ListenerUtil::thrownError($this->translator->trans(
                        'klipper_deposit_sale.deposit_sale.previous_deposit_sale_already_open',
                        [],
                        'validators'
                    ));
                }

                $changeSet = $uow->getEntityChangeSet($object);
                $deviceLastDepositSale = $device->getLastDepositSale();

                if ($create || isset($changeSet['device'])) {
                    $device->setLastDepositSale($object);

                    if (null !== $changeSet['device'][0] && null !== $changeSet['device'][0]->getLastDepositSale()) {
                        $changeSet['device'][0]->setLastDepositSale(null);
                        $classMetadata = $em->getClassMetadata(ClassUtils::getClass($changeSet['device'][0]));
                        $uow->recomputeSingleEntityChangeSet($classMetadata, $changeSet['device'][0]);
                    }

                    $classMetadata = $em->getClassMetadata(ClassUtils::getClass($device));
                    $uow->recomputeSingleEntityChangeSet($classMetadata, $device);
                }

                if (null === $object->getPreviousDepositSale() && null !== $deviceLastDepositSale && $object !== $deviceLastDepositSale) {
                    $object->setPreviousDepositSale($deviceLastDepositSale);

                    $classMetadata = $em->getClassMetadata(ClassUtils::getClass($object));
                    $uow->recomputeSingleEntityChangeSet($classMetadata, $object);
                }
            }
        }
    }

    private function updateProduct(EntityManagerInterface $em, object $object, bool $create = false): void
    {
        $uow = $em->getUnitOfWork();
        $changeSet = $uow->getEntityChangeSet($object);

        if ($object instanceof DepositSaleInterface && null !== $device = $object->getDevice()) {
            if (null !== $device->getProduct() && ($create || (isset($changeSet['device']) && null !== $changeSet['device'][1]))) {
                $object->setProduct($device->getProduct());
                $object->setProductCombination($device->getProductCombination());
            }
        }
    }

    private function updateAccount(EntityManagerInterface $em, object $object): void
    {
        $uow = $em->getUnitOfWork();

        if ($object instanceof DepositSaleInterface && null !== $device = $object->getDevice()) {
            if (null !== $object->getAccount() && (null === $device->getAccount() || $object->getAccount()->getId() !== $device->getAccount()->getId())) {
                $device->setAccount($object->getAccount());

                $classMetadata = $em->getClassMetadata(ClassUtils::getClass($device));
                $uow->recomputeSingleEntityChangeSet($classMetadata, $device);
            }
        }
    }

    private function updateReceiptedAt(EntityManagerInterface $em, object $object, bool $create = false): void
    {
        if ($object instanceof DepositSaleInterface) {
            $uow = $em->getUnitOfWork();
            $changeSet = $uow->getEntityChangeSet($object);

            if (($create && null === $object->getReceiptedAt()) || (!$create && !isset($changeSet['receiptedAt']))) {
                $depositSaleStatus = null !== $object->getStatus() ? $object->getStatus()->getValue() : '';

                if ('received' === $depositSaleStatus) {
                    $object->setReceiptedAt(new \DateTime());

                    $classMetadata = $em->getClassMetadata(ClassUtils::getClass($object));
                    $uow->recomputeSingleEntityChangeSet($classMetadata, $object);
                }
            }
        }
    }

    private function updateClosed(EntityManagerInterface $em, object $object, bool $create = false): void
    {
        if ($object instanceof DepositSaleInterface) {
            $uow = $em->getUnitOfWork();
            $changeSet = $uow->getEntityChangeSet($object);

            if ($create || isset($changeSet['status'])) {
                $closed = null === $object->getStatus() || \in_array($object->getStatus()->getValue(), $this->closedStatues, true);
                $object->setClosed($closed);

                $classMetadata = $em->getClassMetadata(ClassUtils::getClass($object));
                $uow->recomputeSingleEntityChangeSet($classMetadata, $object);
            }
        }
    }

    private function updateAvailable(EntityManagerInterface $em, object $object, bool $create = false): void
    {
        if ($object instanceof DepositSaleInterface) {
            $uow = $em->getUnitOfWork();
            $changeSet = $uow->getEntityChangeSet($object);

            if ($create || isset($changeSet['status'])) {
                $available = null !== $object->getStatus() && \in_array($object->getStatus()->getValue(), $this->availableStatues, true);
                $object->setAvailable($available);

                $classMetadata = $em->getClassMetadata(ClassUtils::getClass($object));
                $uow->recomputeSingleEntityChangeSet($classMetadata, $object);
            }
        }
    }

    private function validateDevice(object $object): void
    {
        if ($object instanceof DepositSaleInterface) {
            if (($object->isClosed() || $object->isAvailable()) && null === $object->getDevice()) {
                ListenerUtil::thrownError($this->translator->trans(
                    'klipper_deposit_sale.deposit_sale.device_required',
                    [],
                    'validators'
                ));
            }
        }
    }

    private function updateDevice(EntityManagerInterface $em, object $object): void
    {
        if (!$object instanceof DepositSaleInterface || null !== $object->getDevice()) {
            return;
        }

        $uow = $em->getUnitOfWork();
        $changeSet = $uow->getEntityChangeSet($object);

        if (isset($changeSet['device']) && null === $object->getDevice()) {
            $oldDevice = $changeSet['device'][0];

            if ($oldDevice instanceof DeviceDepositSaleableInterface
                && $object === $oldDevice->getLastDepositSale()
            ) {
                $oldDevice->setLastDepositSale(null);

                $classMetadata = $em->getClassMetadata(ClassUtils::getClass($oldDevice));
                $uow->recomputeSingleEntityChangeSet($classMetadata, $oldDevice);
            }
        }
    }

    private function updateDeviceStatus(EntityManagerInterface $em, object $object, bool $create = false): void
    {
        if (!$object instanceof DepositSaleInterface || null === $object->getDevice()) {
            return;
        }

        $uow = $em->getUnitOfWork();
        $changeSet = $uow->getEntityChangeSet($object);
        $device = $object->getDevice();

        if ($create || isset($changeSet['device'])) {
            if (isset($changeSet['device'][0])) {
                /** @var DeviceInterface $oldDevice */
                $oldDevice = $changeSet['device'][0];
                $statusOperational = $this->choiceManager->getChoice('device_status', 'in_deposit_sale');

                if (null !== $statusOperational) {
                    $oldDevice->setStatus($statusOperational);

                    $classMetadata = $em->getClassMetadata(ClassUtils::getClass($oldDevice));
                    $uow->recomputeSingleEntityChangeSet($classMetadata, $oldDevice);
                }
            }
        }

        if (null === $device->getTerminatedAt()) {
            $depositSaleStatus = null !== $object->getStatus() ? $object->getStatus()->getValue() : '';

            switch ($depositSaleStatus) {
                case 'received':
                default:
                    $newDeviceStatusValue = 'in_deposit_sale';

                    break;
            }

            if (null === $device->getStatus() || $newDeviceStatusValue !== $device->getStatus()->getValue()) {
                $newDeviceStatus = $this->choiceManager->getChoice('device_status', $newDeviceStatusValue);

                if (null !== $newDeviceStatus) {
                    $device->setStatus($newDeviceStatus);

                    $classMetadata = $em->getClassMetadata(ClassUtils::getClass($device));
                    $uow->recomputeSingleEntityChangeSet($classMetadata, $device);
                }
            }
        }
    }
}

<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Module\DepositSaleBundle\Model;

use Doctrine\Common\Collections\Collection;
use Klipper\Component\DoctrineChoice\Model\ChoiceInterface;
use Klipper\Component\Model\Traits\CurrencyableInterface;
use Klipper\Component\Model\Traits\IdInterface;
use Klipper\Component\Model\Traits\OrganizationalRequiredInterface;
use Klipper\Component\Model\Traits\TimestampableInterface;
use Klipper\Component\Model\Traits\UserTrackableInterface;
use Klipper\Module\DeviceBundle\Model\DeviceInterface;
use Klipper\Module\PartnerBundle\Model\PartnerAddressInterface;
use Klipper\Module\PartnerBundle\Model\Traits\AccountableOptionalInterface;
use Klipper\Module\PartnerBundle\Model\Traits\AccountOwnerableInterface;
use Klipper\Module\ProductBundle\Model\Traits\ProductableOptionalInterface;
use Klipper\Module\ProductBundle\Model\Traits\ProductCombinationableOptionalInterface;
use Klipper\Module\WorkcenterBundle\Model\WorkcenterInterface;

/**
 * Deposit Sale interface.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
interface DepositSaleInterface extends
    AccountOwnerableInterface,
    AccountableOptionalInterface,
    CurrencyableInterface,
    IdInterface,
    OrganizationalRequiredInterface,
    ProductableOptionalInterface,
    ProductCombinationableOptionalInterface,
    TimestampableInterface,
    UserTrackableInterface
{
    /**
     * @return static
     */
    public function setReference(?string $reference);

    public function getReference(): ?string;

    /**
     * @return static
     */
    public function setDevice(?DeviceInterface $device);

    public function getDevice(): ?DeviceInterface;

    /**
     * @return null|int|string
     */
    public function getDeviceId();

    /**
     * @return static
     */
    public function setWorkcenter(?WorkcenterInterface $workcenter);

    public function getWorkcenter(): ?WorkcenterInterface;

    /**
     * @return null|int|string
     */
    public function getWorkcenterId();

    /**
     * @return static
     */
    public function setOriginalAddress(?PartnerAddressInterface $originalAddress);

    public function getOriginalAddress(): ?PartnerAddressInterface;

    /**
     * @return static
     */
    public function setStatus(?ChoiceInterface $status);

    public function getStatus(): ?ChoiceInterface;

    /**
     * @return static
     */
    public function setReceiptedAt(?\DateTimeInterface $receiptedAt);

    public function getReceiptedAt(): ?\DateTimeInterface;

    /**
     * @return static
     */
    public function setClosed(bool $closed);

    public function isClosed(): bool;

    /**
     * @return static
     */
    public function setAvailable(bool $available);

    public function isAvailable(): bool;

    /**
     * @return static
     */
    public function setPreviousDepositSale(?DepositSaleInterface $previousDepositSale);

    public function getPreviousDepositSale(): ?DepositSaleInterface;

    /**
     * @return static
     */
    public function setDescription(?string $description);

    public function getDescription(): ?string;

    /**
     * @return static
     */
    public function setComment(?string $comment);

    public function getComment(): ?string;

    /**
     * @return Collection|DepositSaleAttachmentInterface[]
     */
    public function getAttachments(): Collection;
}

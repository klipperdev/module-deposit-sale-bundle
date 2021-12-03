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

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Klipper\Component\DoctrineChoice\Model\ChoiceInterface;
use Klipper\Component\DoctrineChoice\Validator\Constraints\EntityDoctrineChoice;
use Klipper\Component\Model\Traits\CurrencyableTrait;
use Klipper\Component\Model\Traits\OrganizationalRequiredTrait;
use Klipper\Component\Model\Traits\OwnerableOptionalTrait;
use Klipper\Component\Model\Traits\TimestampableTrait;
use Klipper\Component\Model\Traits\UserTrackableTrait;
use Klipper\Module\DeviceBundle\Model\DeviceInterface;
use Klipper\Module\PartnerBundle\Model\AccountInterface;
use Klipper\Module\PartnerBundle\Model\PartnerAddressInterface;
use Klipper\Module\PartnerBundle\Model\Traits\AccountableOptionalTrait;
use Klipper\Module\ProductBundle\Model\Traits\ProductableTrait;
use Klipper\Module\ProductBundle\Model\Traits\ProductCombinationableTrait;
use Klipper\Module\WorkcenterBundle\Model\WorkcenterInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Deposit Sale model.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @Serializer\ExclusionPolicy("all")
 */
abstract class AbstractDepositSale implements DepositSaleInterface
{
    use AccountableOptionalTrait;
    use CurrencyableTrait;
    use OrganizationalRequiredTrait;
    use OwnerableOptionalTrait;
    use ProductableTrait;
    use ProductCombinationableTrait;
    use TimestampableTrait;
    use UserTrackableTrait;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Klipper\Module\PartnerBundle\Model\AccountInterface"
     * )
     *
     * @Serializer\Expose
     * @Serializer\MaxDepth(3)
     */
    protected ?AccountInterface $account = null;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(min=0, max=80)
     *
     * @Serializer\Expose
     * @Serializer\ReadOnlyProperty
     */
    protected ?string $reference = null;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Klipper\Module\DeviceBundle\Model\DeviceInterface"
     * )
     *
     * @Serializer\Expose
     */
    protected ?DeviceInterface $device = null;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Klipper\Module\WorkcenterBundle\Model\WorkcenterInterface"
     * )
     *
     * @Serializer\Expose
     */
    protected ?WorkcenterInterface $workcenter = null;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Klipper\Module\PartnerBundle\Model\PartnerAddressInterface"
     * )
     *
     * @Assert\NotBlank
     *
     * @Serializer\Expose
     */
    protected ?PartnerAddressInterface $originalAddress = null;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Klipper\Component\DoctrineChoice\Model\ChoiceInterface"
     * )
     *
     * @EntityDoctrineChoice("deposit_sale_status")
     *
     * @Serializer\Expose
     */
    protected ?ChoiceInterface $status = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Assert\Type(type="datetime")
     *
     * @Serializer\Expose
     */
    protected ?\DateTimeInterface $receiptedAt = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(type="boolean")
     *
     * @Serializer\Expose
     * @Serializer\ReadOnlyProperty
     */
    protected bool $closed = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Assert\Type(type="boolean")
     *
     * @Serializer\Expose
     * @Serializer\ReadOnlyProperty
     */
    protected bool $available = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(min=0, max=65535)
     *
     * @Serializer\Expose
     */
    protected ?string $description = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(min=0, max=65535)
     *
     * @Serializer\Expose
     */
    protected ?string $comment = null;

    /**
     * @ORM\OneToOne(
     *     targetEntity="Klipper\Module\DepositSaleBundle\Model\DepositSaleInterface",
     *     cascade={"persist"},
     *     fetch="EAGER"
     * )
     * @ORM\JoinColumn(
     *     name="previous_deposit_sale_id",
     *     referencedColumnName="id",
     *     onDelete="SET NULL",
     *     nullable=true
     * )
     *
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     * @Serializer\Groups({"ViewsDetails", "View"})
     */
    protected ?DepositSaleInterface $previousDepositSale = null;

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setDevice(?DeviceInterface $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getDevice(): ?DeviceInterface
    {
        return $this->device;
    }

    public function getDeviceId()
    {
        return null !== $this->getDevice()
            ? $this->getDevice()->getId()
            : null;
    }

    public function setWorkcenter(?WorkcenterInterface $workcenter): self
    {
        $this->workcenter = $workcenter;

        return $this;
    }

    public function getWorkcenter(): ?WorkcenterInterface
    {
        return $this->workcenter;
    }

    public function getWorkcenterId()
    {
        return null !== $this->getWorkcenter()
            ? $this->getWorkcenter()->getId()
            : null;
    }

    public function setOriginalAddress(?PartnerAddressInterface $originalAddress): self
    {
        $this->originalAddress = $originalAddress;

        return $this;
    }

    public function getOriginalAddress(): ?PartnerAddressInterface
    {
        return $this->originalAddress;
    }

    public function setStatus(?ChoiceInterface $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?ChoiceInterface
    {
        return $this->status;
    }

    public function setReceiptedAt(?\DateTimeInterface $receiptedAt): self
    {
        $this->receiptedAt = $receiptedAt;

        return $this;
    }

    public function getReceiptedAt(): ?\DateTimeInterface
    {
        return $this->receiptedAt;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setPreviousDepositSale(?DepositSaleInterface $previousDepositSale): self
    {
        $this->previousDepositSale = $previousDepositSale;

        return $this;
    }

    public function getPreviousDepositSale(): ?DepositSaleInterface
    {
        return $this->previousDepositSale;
    }
}

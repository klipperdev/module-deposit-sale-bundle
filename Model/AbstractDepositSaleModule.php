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
use Klipper\Component\Model\Traits\EnableTrait;
use Klipper\Component\Model\Traits\OrganizationalRequiredTrait;
use Klipper\Component\Model\Traits\TimestampableTrait;
use Klipper\Module\DepositSaleBundle\Model\Traits\DepositSaleModuleableInterface;
use Klipper\Module\PartnerBundle\Model\AccountInterface;
use Klipper\Module\PartnerBundle\Model\PartnerAddressInterface;
use Klipper\Module\PartnerBundle\Model\Traits\AccountableTrait;
use Klipper\Module\WorkcenterBundle\Model\WorkcenterInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Deposit Sale module model.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 *
 * @Serializer\ExclusionPolicy("all")
 */
abstract class AbstractDepositSaleModule implements DepositSaleModuleInterface
{
    use AccountableTrait;
    use EnableTrait;
    use OrganizationalRequiredTrait;
    use TimestampableTrait;

    /**
     * @ORM\OneToOne(
     *     targetEntity="Klipper\Module\PartnerBundle\Model\AccountInterface",
     *     inversedBy="depositSaleModule"
     * )
     *
     * @Assert\NotNull
     *
     * @Serializer\Type("AssociationId")
     * @Serializer\Expose
     */
    protected ?AccountInterface $account = null;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Klipper\Module\WorkcenterBundle\Model\WorkcenterInterface"
     * )
     *
     * @Assert\NotBlank
     *
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    protected ?WorkcenterInterface $workcenter = null;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Klipper\Module\PartnerBundle\Model\PartnerAddressInterface"
     * )
     *
     * @Serializer\Expose
     * @Serializer\MaxDepth(2)
     */
    protected ?PartnerAddressInterface $defaultOriginalAddress = null;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Klipper\Component\DoctrineChoice\Model\ChoiceInterface"
     * )
     *
     * @EntityDoctrineChoice("deposit_sale_status")
     *
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    protected ?ChoiceInterface $defaultStatus = null;

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
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(min=0, max=65535)
     *
     * @Serializer\Expose
     */
    protected ?string $excludedScope = null;

    public function setAccount(?AccountInterface $account): self
    {
        $this->account = $account;

        if ($account instanceof DepositSaleModuleableInterface) {
            $account->setDepositSaleModule($this);
        }

        return $this;
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

    public function setDefaultOriginalAddress(?PartnerAddressInterface $defaultOriginalAddress): self
    {
        $this->defaultOriginalAddress = $defaultOriginalAddress;

        return $this;
    }

    public function getDefaultOriginalAddress(): ?PartnerAddressInterface
    {
        return $this->defaultOriginalAddress;
    }

    public function setDefaultStatus(?ChoiceInterface $defaultStatus): self
    {
        $this->defaultStatus = $defaultStatus;

        return $this;
    }

    public function getDefaultStatus(): ?ChoiceInterface
    {
        return $this->defaultStatus;
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

    public function setExcludedScope(?string $excludedScope): self
    {
        $this->excludedScope = $excludedScope;

        return $this;
    }

    public function getExcludedScope(): ?string
    {
        return $this->excludedScope;
    }
}

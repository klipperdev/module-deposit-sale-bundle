<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Module\DepositSaleBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Klipper\Module\DepositSaleBundle\Model\DepositSaleModuleInterface;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
trait DepositSaleModuleableTrait
{
    /**
     * @ORM\OneToOne(
     *     targetEntity="Klipper\Module\DepositSaleBundle\Model\DepositSaleModuleInterface",
     *     mappedBy="account"
     * )
     * @ORM\JoinColumn(
     *     name="deposit_sale_module_id",
     *     referencedColumnName="id",
     *     onDelete="SET NULL",
     *     nullable=true
     * )
     *
     * @Serializer\Expose
     * @Serializer\ReadOnlyProperty
     * @Serializer\MaxDepth(3)
     * @Serializer\Groups({"View", "ViewsDetails"})
     */
    protected ?DepositSaleModuleInterface $depositSaleModule = null;

    /**
     * @see DepositSaleModuleableInterface::setOperationBreakdown()
     */
    public function setDepositSaleModule(?DepositSaleModuleInterface $depositSaleModule): self
    {
        $this->depositSaleModule = $depositSaleModule;

        return $this;
    }

    /**
     * @see DepositSaleModuleableInterface::getBreakdown()
     */
    public function getDepositSaleModule(): ?DepositSaleModuleInterface
    {
        return $this->depositSaleModule;
    }
}

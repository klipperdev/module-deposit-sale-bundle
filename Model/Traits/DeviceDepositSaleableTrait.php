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
use Klipper\Module\DepositSaleBundle\Model\DepositSaleInterface;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
trait DeviceDepositSaleableTrait
{
    /**
     * @ORM\OneToOne(
     *     targetEntity="Klipper\Module\DepositSaleBundle\Model\DepositSaleInterface"
     * )
     * @ORM\JoinColumn(
     *     name="last_deposit_sale_id",
     *     referencedColumnName="id",
     *     onDelete="SET NULL",
     *     nullable=true
     * )
     *
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     * @Serializer\ReadOnlyProperty
     */
    protected ?DepositSaleInterface $lastDepositSale = null;

    public function setLastDepositSale(?DepositSaleInterface $lastDepositSale): self
    {
        $this->lastDepositSale = $lastDepositSale;

        return $this;
    }

    public function getLastDepositSale(): ?DepositSaleInterface
    {
        return $this->lastDepositSale;
    }

    /**
     * @Serializer\VirtualProperty
     *
     * @return null|int|string
     */
    public function getLastDepositSaleId()
    {
        return null !== $this->getLastDepositSale() ? $this->getLastDepositSale()->getId() : null;
    }
}

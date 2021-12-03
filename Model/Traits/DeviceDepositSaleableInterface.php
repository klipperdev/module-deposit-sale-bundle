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

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
interface DeviceDepositSaleableInterface
{
    /**
     * @return static
     */
    public function setLastDepositSale(?DepositSaleInterface $lastDepositSale);

    public function getLastDepositSale(): ?DepositSaleInterface;

    /**
     * @return null|int|string
     */
    public function getLastDepositSaleId();
}

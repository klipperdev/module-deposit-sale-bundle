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

use Klipper\Module\DepositSaleBundle\Model\DepositSaleModuleInterface;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
interface DepositSaleModuleableInterface
{
    /**
     * @return static
     */
    public function setDepositSaleModule(?DepositSaleModuleInterface $depositSaleModule);

    public function getDepositSaleModule(): ?DepositSaleModuleInterface;
}

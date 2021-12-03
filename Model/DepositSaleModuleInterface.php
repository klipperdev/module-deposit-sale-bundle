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

use Klipper\Component\DoctrineChoice\Model\ChoiceInterface;
use Klipper\Component\Model\Traits\EnableInterface;
use Klipper\Component\Model\Traits\IdInterface;
use Klipper\Component\Model\Traits\OrganizationalRequiredInterface;
use Klipper\Component\Model\Traits\TimestampableInterface;
use Klipper\Module\PartnerBundle\Model\PartnerAddressInterface;
use Klipper\Module\PartnerBundle\Model\Traits\AccountableRequiredInterface;
use Klipper\Module\WorkcenterBundle\Model\WorkcenterInterface;

/**
 * Deposit Sale module interface.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
interface DepositSaleModuleInterface extends
    AccountableRequiredInterface,
    EnableInterface,
    IdInterface,
    OrganizationalRequiredInterface,
    TimestampableInterface
{
    /**
     * @return static
     */
    public function setWorkcenter(?WorkcenterInterface $workcenter);

    public function getWorkcenter(): ?WorkcenterInterface;

    /**
     * @return static
     */
    public function setDefaultOriginalAddress(?PartnerAddressInterface $defaultOriginalAddress);

    public function getDefaultOriginalAddress(): ?PartnerAddressInterface;

    /**
     * @return static
     */
    public function setDefaultStatus(?ChoiceInterface $defaultStatus);

    public function getDefaultStatus(): ?ChoiceInterface;

    /**
     * @return static
     */
    public function setComment(?string $comment);

    public function getComment(): ?string;

    /**
     * @return static
     */
    public function setExcludedScope(?string $excludedScope);

    public function getExcludedScope(): ?string;
}

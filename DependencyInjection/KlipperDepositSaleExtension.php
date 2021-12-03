<?php

/*
 * This file is part of the Klipper package.
 *
 * (c) François Pluchino <francois.pluchino@klipper.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Klipper\Module\DepositSaleBundle\DependencyInjection;

use Klipper\Module\DepositSaleBundle\Doctrine\Listener\DepositSaleSubscriber;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class KlipperDepositSaleExtension extends Extension
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->configDepositSale($container, $loader, $config['deposit_sale']);
    }

    /**
     * @throws
     */
    private function configDepositSale(ContainerBuilder $container, LoaderInterface $loader, array $config): void
    {
        $loader->load('doctrine_subscriber.xml');

        $def = $container->getDefinition(DepositSaleSubscriber::class);

        $def->replaceArgument(3, array_unique(array_merge($config['closed_statuses'], [
            'sold',
        ])));

        $def->replaceArgument(4, array_unique(array_merge($config['available_statuses'], [
            'in_deposit_sale',
        ])));
    }
}

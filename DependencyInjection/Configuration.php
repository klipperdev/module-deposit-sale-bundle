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

use Klipper\Bundle\SecurityBundle\DependencyInjection\NodeUtils;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your config files.
 *
 * @author François Pluchino <francois.pluchino@klipper.dev>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('klipper_deposit_sale');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->append($this->getDepositSaleNode())
            ->end()
        ;

        return $treeBuilder;
    }

    private function getDepositSaleNode(): NodeDefinition
    {
        return NodeUtils::createArrayNode('deposit_sale')
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('closed_statuses')
            ->scalarPrototype()->end()
            ->end()
            ->arrayNode('available_statuses')
            ->scalarPrototype()->end()
            ->end()
            ->end()
        ;
    }
}

<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('umanit_sylius_product_variant_attribute_plugin');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('product_variant_model')
                    ->defaultValue('%sylius.model.product_variant.class%')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

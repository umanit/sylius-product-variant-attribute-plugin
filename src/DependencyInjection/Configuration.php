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
                ->booleanNode('rename_product_attribute_menu_entry')
                    ->info('The "Attributes" entry in the "Catalog" menu should be renamed to "Products attributes"')
                    ->defaultTrue()
                ->end()
                ->scalarNode('product_variant_model')
                    ->info('The FQCN of the product variant model. Defaults to the parameter %sylius.model.product_variant.class%')
                    ->defaultValue('%sylius.model.product_variant.class%')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Umanit\SyliusProductVariantAttributePlugin\Controller\ProductVariantAttributeController;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttribute;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeTranslation;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeTranslationInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeValue;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeValueInterface;
use Umanit\SyliusProductVariantAttributePlugin\Form\Type\ProductVariantAttributeTranslationType;
use Umanit\SyliusProductVariantAttributePlugin\Form\Type\ProductVariantAttributeType;
use Umanit\SyliusProductVariantAttributePlugin\Form\Type\ProductVariantAttributeValueType;
use Umanit\SyliusProductVariantAttributePlugin\Repository\ProductVariantAttributeValueRepository;

final class UmanitSyliusProductVariantAttributeExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yaml');

        $container->setParameter(
            'umanit_sylius_product_variant_attribute_plugin.rename_product_attribute_menu_entry',
            $config['rename_product_attribute_menu_entry']
        );
        $container->setParameter(
            'umanit_sylius_product_variant_attribute_plugin.product_variant_model',
            $config['product_variant_model']
        );
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        $this->prependAttribute($container, $config);
    }

    private function prependAttribute(ContainerBuilder $container, array $config): void
    {
        if (!$container->hasExtension('sylius_attribute')) {
            return;
        }

        $container->prependExtensionConfig('sylius_attribute', [
            'resources' => [
                'product_variant' => [
                    'subject'         => $config['product_variant_model'],
                    'attribute'       => [
                        'classes'     => [
                            'model'      => ProductVariantAttribute::class,
                            'interface'  => ProductVariantAttributeInterface::class,
                            'controller' => ProductVariantAttributeController::class,
                            'form'       => ProductVariantAttributeType::class,
                        ],
                        'translation' => [
                            'classes' => [
                                'model'     => ProductVariantAttributeTranslation::class,
                                'interface' => ProductVariantAttributeTranslationInterface::class,
                                'form'      => ProductVariantAttributeTranslationType::class,
                            ],
                        ],
                    ],
                    'attribute_value' => [
                        'classes' => [
                            'model'      => ProductVariantAttributeValue::class,
                            'interface'  => ProductVariantAttributeValueInterface::class,
                            'repository' => ProductVariantAttributeValueRepository::class,
                            'form'       => ProductVariantAttributeValueType::class,
                        ],
                    ],
                ],
            ],
        ]);
    }
}

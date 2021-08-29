<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Form\Extension;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Umanit\SyliusProductVariantAttributePlugin\Form\EventSubscriber\BuildAttributesFormSubscriber;
use Umanit\SyliusProductVariantAttributePlugin\Form\Type\ProductVariantAttributeValueType;

final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    /** @var FactoryInterface */
    private $attributeValueFactory;

    /** @var TranslationLocaleProviderInterface */
    private $localeProvider;

    public function __construct(
        FactoryInterface $attributeValueFactory,
        TranslationLocaleProviderInterface $localeProvider
    ) {
        $this->attributeValueFactory = $attributeValueFactory;
        $this->localeProvider = $localeProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new BuildAttributesFormSubscriber($this->attributeValueFactory, $this->localeProvider))
            ->add('attributes', CollectionType::class, [
                'entry_type'   => ProductVariantAttributeValueType::class,
                'required'     => false,
                'prototype'    => true,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => false,
            ])
        ;

    }

    public static function getExtendedTypes(): array
    {
        return [ProductVariantType::class];
    }
}

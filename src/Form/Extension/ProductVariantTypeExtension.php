<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Form\Extension;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Umanit\SyliusProductVariantAttributePlugin\Form\Type\ProductVariantAttributeValueType;

final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('attributes', CollectionType::class, [
            'entry_type'   => ProductVariantAttributeValueType::class,
            'required'     => false,
            'prototype'    => true,
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label'        => false,
        ]);

    }

    public function getExtendedTypes(): array
    {
        return [ProductVariantType::class];
    }
}

<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Form\Type;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeTranslationType;

final class ProductVariantAttributeTranslationType extends AttributeTranslationType
{
    public function getBlockPrefix(): string
    {
        return 'umanit_product_variant_attribute_translation';
    }
}

<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Form\Type;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType;

final class ProductVariantAttributeValueType extends AttributeValueType
{
    public function getBlockPrefix(): string
    {
        return 'umanit_product_variant_attribute_value';
    }
}

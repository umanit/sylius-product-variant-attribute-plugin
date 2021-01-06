<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Entity;

use Sylius\Component\Attribute\Model\Attribute as BaseAttribute;
use Sylius\Component\Attribute\Model\AttributeTranslationInterface;

class ProductVariantAttribute extends BaseAttribute implements ProductVariantAttributeInterface
{
    protected function createTranslation(): AttributeTranslationInterface
    {
        return new ProductVariantAttributeTranslation();
    }
}

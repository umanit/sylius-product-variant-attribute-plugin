<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Entity;

use Sylius\Component\Attribute\Model\AttributeValueInterface as BaseAttributeValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

interface ProductVariantAttributeValueInterface extends BaseAttributeValueInterface
{
    public function getProductVariant(): ?ProductVariantInterface;

    public function setProductVariant(?ProductVariantInterface $productVariant): void;
}

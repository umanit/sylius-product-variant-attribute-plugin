<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Entity;

use Sylius\Component\Attribute\Model\AttributeValue as BaseAttributeValue;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

class ProductVariantAttributeValue extends BaseAttributeValue implements ProductVariantAttributeValueInterface
{
    public function getProductVariant(): ?ProductVariantInterface
    {
        $subject = $this->getSubject();

        /** @var ProductVariantInterface|null $subject */
        Assert::nullOrIsInstanceOf($subject, ProductVariantInterface::class);

        return $subject;
    }

    public function setProductVariant(?ProductVariantInterface $productVariant): void
    {
        $this->setSubject($productVariant);
    }
}

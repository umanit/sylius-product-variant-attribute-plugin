<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Webmozart\Assert\Assert;

trait ProductVariantTrait
{
    /** @var Collection|AttributeValueInterface[] */
    protected $attributes;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
    }

    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(?AttributeValueInterface $attribute): void
    {
        /** @var ProductVariantAttributeValueInterface $attribute */
        Assert::isInstanceOf(
            $attribute,
            ProductVariantAttributeValueInterface::class,
            'Attribute objects added to a ProductVariant object have to implement ProductVariantAttributeValueInterface'
        );

        if (!$this->hasAttribute($attribute)) {
            $attribute->setProductVariant($this);
            $this->attributes->add($attribute);
        }
    }

    public function removeAttribute(?AttributeValueInterface $attribute): void
    {
        /** @var ProductVariantAttributeValueInterface $attribute */
        Assert::isInstanceOf(
            $attribute,
            ProductVariantAttributeValueInterface::class,
            'Attribute objects removed from a ProductVariant object have to implement ProductVariantAttributeValueInterface'
        );

        if ($this->hasAttribute($attribute)) {
            $this->attributes->removeElement($attribute);
            $attribute->setProductVariant(null);
        }
    }

    public function hasAttribute(AttributeValueInterface $attribute): bool
    {
        return $this->attributes->contains($attribute);
    }

    public function hasAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): bool
    {
        $localeCode = $localeCode ?: $this->getTranslation()->getLocale();

        foreach ($this->attributes as $attribute) {
            if (
                $attributeCode === $attribute->getAttribute()->getCode() &&
                ($localeCode === $attribute->getLocaleCode() || null === $attribute->getLocaleCode())
            ) {
                return true;
            }
        }

        return false;
    }

    public function getAttributeByCodeAndLocale(string $attributeCode, ?string $localeCode = null): ?AttributeValueInterface
    {
        if (null === $localeCode) {
            $localeCode = $this->getTranslation()->getLocale();
        }

        foreach ($this->attributes as $attribute) {
            if (
                $attributeCode === $attribute->getAttribute()->getCode() &&
                ($localeCode === $attribute->getLocaleCode() || null === $attribute->getLocaleCode())) {
                return $attribute;
            }
        }

        return null;
    }

    public function getAttributesByLocale(
        string $localeCode,
        string $fallbackLocaleCode,
        ?string $baseLocaleCode = null
    ): Collection {
        if (null === $baseLocaleCode || $baseLocaleCode === $fallbackLocaleCode) {
            $baseLocaleCode = $fallbackLocaleCode;
            $fallbackLocaleCode = null;
        }

        $attributes = $this->attributes->filter(
            function (ProductVariantAttributeValueInterface $attribute) use ($baseLocaleCode) {
                return $baseLocaleCode === $attribute->getLocaleCode() || null === $attribute->getLocaleCode();
            }
        );

        $attributesWithFallback = [];
        foreach ($attributes as $attribute) {
            $attributesWithFallback[] = $this->getAttributeInDifferentLocale($attribute, $localeCode, $fallbackLocaleCode);
        }

        return new ArrayCollection($attributesWithFallback);
    }

    protected function getAttributeInDifferentLocale(
        ProductVariantAttributeValueInterface $attributeValue,
        string $localeCode,
        ?string $fallbackLocaleCode = null
    ): AttributeValueInterface {
        if (!$this->hasNotEmptyAttributeByCodeAndLocale($attributeValue->getCode(), $localeCode)) {
            if (
                null !== $fallbackLocaleCode &&
                $this->hasNotEmptyAttributeByCodeAndLocale($attributeValue->getCode(), $fallbackLocaleCode)
            ) {
                return $this->getAttributeByCodeAndLocale($attributeValue->getCode(), $fallbackLocaleCode);
            }

            return $attributeValue;
        }

        return $this->getAttributeByCodeAndLocale($attributeValue->getCode(), $localeCode);
    }

    protected function hasNotEmptyAttributeByCodeAndLocale(string $attributeCode, string $localeCode): bool
    {
        $attributeValue = $this->getAttributeByCodeAndLocale($attributeCode, $localeCode);

        if (null === $attributeValue) {
            return false;
        }

        $value = $attributeValue->getValue();

        return !('' === $value || null === $value || [] === $value);
    }
}

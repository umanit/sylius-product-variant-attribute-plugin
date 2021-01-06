<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Form\EventSubscriber;

use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeValueInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class BuildAttributesFormSubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT  => 'postSubmit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $productVariant = $event->getData();

        /** @var ProductVariantInterface $productVariant */
        Assert::isInstanceOf($productVariant, ProductVariantInterface::class);

        $defaultLocaleCode = $this->localeProvider->getDefaultLocaleCode();

        $attributes = $productVariant
            ->getAttributes()
            ->filter(
                static function (ProductVariantAttributeValueInterface $attribute) use ($defaultLocaleCode) {
                    return $attribute->getLocaleCode() === $defaultLocaleCode;
                }
            )
        ;

        foreach ($attributes as $attribute) {
            $this->resolveLocalizedAttributes($productVariant, $attribute);
        }
    }

    public function postSubmit(FormEvent $event): void
    {
        $productVariant = $event->getData();

        /** @var ProductVariantInterface $productVariant */
        Assert::isInstanceOf($productVariant, ProductVariantInterface::class);

        /** @var AttributeValueInterface $attribute */
        foreach ($productVariant->getAttributes() as $attribute) {
            if (null === $attribute->getValue()) {
                $productVariant->removeAttribute($attribute);
            }
        }
    }

    private function resolveLocalizedAttributes(
        ProductVariantInterface $productVariant,
        ProductVariantAttributeValueInterface $attribute
    ): void {
        $localeCodes = $this->localeProvider->getDefinedLocalesCodes();

        foreach ($localeCodes as $localeCode) {
            if (!$productVariant->hasAttributeByCodeAndLocale($attribute->getCode(), $localeCode)) {
                $attributeValue = $this->createProductVariantAttributeValue($attribute->getAttribute(), $localeCode);
                $productVariant->addAttribute($attributeValue);
            }
        }
    }

    private function createProductVariantAttributeValue(
        ProductVariantAttributeInterface $attribute,
        string $localeCode
    ): ProductVariantAttributeValueInterface {
        /** @var ProductVariantAttributeValueInterface $attributeValue */
        $attributeValue = $this->attributeValueFactory->createNew();
        $attributeValue->setAttribute($attribute);
        $attributeValue->setLocaleCode($localeCode);

        return $attributeValue;
    }
}

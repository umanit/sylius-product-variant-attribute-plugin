<?php

declare(strict_types=1);

namespace spec\Umanit\SyliusProductVariantAttributePlugin\Form\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeValueInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantInterface;

final class BuildAttributesFormSubscriberSpec extends ObjectBehavior
{
    function let(FactoryInterface $attributeValueFactory, TranslationLocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($attributeValueFactory, $localeProvider);
    }

    function it_subscribes_to_event(): void
    {
        static::getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT  => 'postSubmit',
        ])
        ;
    }

    function it_adds_attribute_values_in_different_locales_to_a_product_variant(
        FactoryInterface $attributeValueFactory,
        TranslationLocaleProviderInterface $localeProvider,
        FormEvent $event,
        ProductVariantInterface $productVariant,
        ProductVariantAttributeInterface $attribute,
        ProductVariantAttributeValueInterface $attributeValue,
        ProductVariantAttributeValueInterface $newAttributeValue
    ): void {
        $event->getData()->willReturn($productVariant);

        $localeProvider->getDefinedLocalesCodes()->willReturn(['en_US', 'pl_PL']);
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $attributeValue->getAttribute()->willReturn($attribute);
        $attributeValue->getLocaleCode()->willReturn('en_US');
        $attributeValue->getCode()->willReturn('mug_material');

        $attributes = new ArrayCollection([$attributeValue->getWrappedObject()]);
        $productVariant->getAttributes()->willReturn($attributes);
        $productVariant->hasAttributeByCodeAndLocale('mug_material', 'en_US')->willReturn(true);
        $productVariant->hasAttributeByCodeAndLocale('mug_material', 'pl_PL')->willReturn(false);

        $attributeValueFactory->createNew()->willReturn($newAttributeValue);
        $newAttributeValue->setAttribute($attribute)->shouldBeCalled();
        $newAttributeValue->setLocaleCode('pl_PL')->shouldBeCalled();
        $productVariant->addAttribute($newAttributeValue)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_removes_empty_attribute_values_in_different_locales(
        FormEvent $event,
        ProductVariantInterface $productVariant,
        ProductVariantAttributeInterface $attribute,
        ProductVariantAttributeValueInterface $attributeValue,
        ProductVariantAttributeValueInterface $attributeValue2
    ): void {
        $event->getData()->willReturn($productVariant);

        $attributes = new ArrayCollection([$attributeValue->getWrappedObject(), $attributeValue2->getWrappedObject()]);
        $productVariant->getAttributes()->willReturn($attributes);

        $attribute->getStorageType()->willReturn('text');

        $attributeValue->getValue()->willReturn(null);
        $attributeValue2->getValue()->willReturn('yellow');

        $productVariant->removeAttribute($attributeValue)->shouldBeCalled();

        $this->postSubmit($event);
    }

    function it_throws_an_invalid_argument_exception_if_data_is_not_a_product_variant(FormEvent $event, \stdClass $stdObject): void
    {
        $event->getData()->willReturn($stdObject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('preSetData', [$event])
        ;
    }

    function it_throws_an_invalid_argument_exception_if_data_is_not_a_product_variant_during_submit(FormEvent $event, \stdClass $stdObject): void
    {
        $event->getData()->willReturn($stdObject);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('postSubmit', [$event])
        ;
    }
}

<?php

declare(strict_types=1);

namespace spec\Umanit\SyliusProductVariantAttributePlugin\Event\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\MockInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeValue;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeValueInterface;

final class SelectProductVariantAttributeChoiceRemoveListenerSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(ProductVariantAttributeValue::class);
    }

    function it_removes_select_product_variant_attribute_choices(
        LifecycleEventArgs $event,
        EntityManagerInterface $entityManager,
        ProductAttributeValueRepositoryInterface $productVariantAttributeValueRepository,
        ProductAttributeInterface $productAttribute,
        ProductVariantAttributeValueInterface $productVariantAttributeValue
    ): void {
        $event->getEntity()->willReturn($productAttribute);
        $event->getEntityManager()->willReturn($entityManager);

        $productAttribute->getType()->willReturn(SelectAttributeType::TYPE);

        /** @var UnitOfWork|MockInterface $unitOfWork */
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('getEntityChangeSet')->withArgs([$productAttribute->getWrappedObject()])->andReturn([
            'configuration' => [
                [
                    'choices' => [
                        '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                        '1739bc61-9e42-4c80-8b9a-f97f0579cccb' => 'Pineapple',
                    ],
                ],
                [
                    'choices' => [
                        '8ec40814-adef-4194-af91-5559b5f19236' => 'Banana',
                    ],
                ],
            ],
        ])
        ;

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $entityManager
            ->getRepository(ProductVariantAttributeValue::class)
            ->willReturn($productVariantAttributeValueRepository)
        ;
        $productVariantAttributeValueRepository
            ->findByJsonChoiceKey('1739bc61-9e42-4c80-8b9a-f97f0579cccb')
            ->willReturn([$productVariantAttributeValue])
        ;

        $productVariantAttributeValue->getValue()->willReturn([
            '8ec40814-adef-4194-af91-5559b5f19236',
            '1739bc61-9e42-4c80-8b9a-f97f0579cccb',
        ])
        ;

        $productVariantAttributeValue->setValue(['8ec40814-adef-4194-af91-5559b5f19236'])->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->postUpdate($event);
    }

    function it_does_nothing_if_an_entity_is_not_a_product_variant_attribute(
        EntityManagerInterface $entityManager,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('wrongObject');

        $entityManager
            ->getRepository(ProductVariantAttributeValue::class)
            ->shouldNotBeCalled()
        ;
        $entityManager->flush()->shouldNotBeCalled();
    }
}

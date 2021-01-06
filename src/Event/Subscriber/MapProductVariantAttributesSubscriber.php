<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Event\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantAttributeValue;

class MapProductVariantAttributesSubscriber implements EventSubscriber
{
    /** @var string */
    private $productVariantClass;

    public function __construct(string $productVariantClass)
    {
        $this->productVariantClass = $productVariantClass;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArguments): void
    {
        $metadata = $eventArguments->getClassMetadata();

        if ($this->productVariantClass !== $metadata->getName()) {
            return;
        }

        $metadata->mapOneToMany([
            'fieldName'     => 'attributes',
            'targetEntity'  => ProductVariantAttributeValue::class,
            'mappedBy'      => 'subject',
            'orphanRemoval' => true,
            'cascade'       => ['all'],
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Repository;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface ProductVariantAttributeValueRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $choiceKey
     *
     * @return array|ProductAttributeValueInterface[]
     */
    public function findByJsonChoiceKey(string $choiceKey): array;
}

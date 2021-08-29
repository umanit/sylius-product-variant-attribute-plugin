<?php

declare(strict_types=1);

namespace Tests\Umanit\SyliusProductVariantAttributePlugin\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Product\Model\ProductVariantTranslation;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantInterface;
use Umanit\SyliusProductVariantAttributePlugin\Entity\ProductVariantTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
class ProductVariant extends BaseProductVariant implements ProductVariantInterface
{
    use ProductVariantTrait {
        __construct as attributesConstruct;
    }

    public function __construct()
    {
        parent::__construct();

        $this->attributesConstruct();
    }

    protected function createTranslation(): ProductVariantTranslationInterface
    {
        return new ProductVariantTranslation();
    }
}

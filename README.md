<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

# UmanIT

## Sylius Product Variant Attribute Plugin

Add attributes on your products variants.

## Install

Register the plugin to your `config/bundles.php`

```php
<?php

return [
    // ...
    Umanit\SyliusProductVariantAttributePlugin\UmanitSyliusProductVariantAttributePlugin::class => ['all' => true],
];
```

Import the configuration file, for example in `config/packages/umanit_sylius_product_variant_attribute_plugin.yaml`

```yaml
imports:
    - { resource: '@UmanitSyliusProductVariantAttributePlugin/Resources/config/config.yaml' }
```

Import the routing file, for example in `config/routes/sylius_admin.yaml`

```yaml
umanit_sylius_product_variant_attribute_plugin:
    resource: "@UmanitSyliusProductVariantAttributePlugin/Resources/config/admin_routing.yaml"
    prefix: /admin
```

Update your `ProductVariant` entity by implementing the `ProductVariantInterface` and using the `ProductVariantTrait`

```php
<?php

declare(strict_types=1);

namespace App\Entity\Product;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
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
```

Finally, don't forget to update your database!

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Usage

Like in product edition, variants now has an `Attributes` tab in which you can add attributes. There is no distinction
between product and variant attributes list; Existing attributes of your application can be used in the entity of your
choice.

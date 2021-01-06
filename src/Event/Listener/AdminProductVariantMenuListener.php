<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Event\Listener;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Knp\Menu\Util\MenuManipulator;
use Sylius\Bundle\AdminBundle\Event\ProductVariantMenuBuilderEvent;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class AdminProductVariantMenuListener
{
    /** @var MenuManipulator */
    private $menuManipulator;
    /** @var bool */
    private $renameProductAttributeMenuEntry;

    public function __construct(MenuManipulator $menuManipulator, bool $renameProductAttributeMenuEntry)
    {
        $this->menuManipulator = $menuManipulator;
        $this->renameProductAttributeMenuEntry = $renameProductAttributeMenuEntry;
    }

    public function addAttributesMainMenu(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        $catalog = $menu->getChild('catalog');

        if (null === $catalog) {
            return;
        }

        $attributes = $catalog->addChild(
            'variant_attributes',
            ['route' => 'sylius_admin_product_variant_attribute_index']
        );

        $attributes
            ->setLabel('umanit_sylius_product_variant_attribute_plugin.menu.admin.main.catalog.variant_attributes')
            ->setLabelAttribute('icon', 'cubes')
        ;

        $this->menuManipulator->moveToPosition($attributes, 4);

        if ($this->renameProductAttributeMenuEntry) {
            $this->renameProductAttributeMenuEntry($catalog);
        }
    }

    public function addAttributesFormMenu(ProductVariantMenuBuilderEvent $event): void
    {
        /** @var MenuItem $menu */
        $menu = $event->getMenu();

        $attributes = $menu->addChild('attributes');
        $attributes
            ->setAttribute(
                'template',
                '@UmanitSyliusProductVariantAttributePlugin/Admin/ProductVariant/Tab/_attributes.html.twig'
            )
            ->setLabel('sylius.ui.attributes')
        ;

        $this->menuManipulator->moveToPosition($attributes, 1);
    }

    private function renameProductAttributeMenuEntry(ItemInterface $catalog): void
    {
        $attributes = $catalog->getChild('attributes');

        if (null === $attributes) {
            return;
        }

        $attributes->setLabel('umanit_sylius_product_variant_attribute_plugin.menu.admin.main.catalog.product_attributes');
    }
}

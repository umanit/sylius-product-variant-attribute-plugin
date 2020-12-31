<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Event\Listener;

use Knp\Menu\MenuItem;
use Sylius\Bundle\AdminBundle\Event\ProductVariantMenuBuilderEvent;

class AdminProductVariantMenuListener
{
    public function addAttributesMenu(ProductVariantMenuBuilderEvent $event): void
    {
        /** @var MenuItem $menu */
        $menu = $event->getMenu();
        $menu
            ->addChild('attributes')
            ->setAttribute(
                'template',
                '@UmanitSyliusProductVariantAttributePlugin/Admin/ProductVariant/Tab/_attributes.html.twig'
            )
            ->setLabel('sylius.ui.attributes')
        ;
    }
}

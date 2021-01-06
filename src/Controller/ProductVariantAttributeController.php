<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Controller;

use Sylius\Bundle\ProductBundle\Controller\ProductAttributeController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Umanit\SyliusProductVariantAttributePlugin\Form\Type\ProductVariantAttributeChoiceType;

class ProductVariantAttributeController extends ProductAttributeController
{
    public function renderAttributesAction(Request $request): Response
    {
        $template = $request->attributes->get('template', '@SyliusAttribute/attributeChoice.html.twig');

        $form = $this->get('form.factory')->create(ProductVariantAttributeChoiceType::class, null, [
            'multiple' => true,
        ])
        ;

        return $this->render($template, ['form' => $form->createView()]);
    }

    public function renderAttributeValueFormsAction(Request $request): Response
    {
        $template = $request->attributes->get('template', '@SyliusAttribute/attributeValueForms.html.twig');

        $form = $this->get('form.factory')->create(ProductVariantAttributeChoiceType::class, null, [
            'multiple' => true,
        ])
        ;
        $form->handleRequest($request);

        $attributes = $form->getData();
        if (null === $attributes) {
            throw new BadRequestHttpException();
        }

        $localeCodes = $this->get('sylius.translation_locale_provider')->getDefinedLocalesCodes();

        $forms = [];
        foreach ($attributes as $attribute) {
            $forms[$attribute->getCode()] = $this->getAttributeFormsInAllLocales($attribute, $localeCodes);
        }

        return $this->render($template, [
            'forms'    => $forms,
            'count'    => $request->query->get('count'),
            'metadata' => $this->metadata,
        ]);
    }
}

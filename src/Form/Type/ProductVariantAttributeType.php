<?php

declare(strict_types=1);

namespace Umanit\SyliusProductVariantAttributePlugin\Form\Type;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductVariantAttributeType extends AttributeType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('position', IntegerType::class, [
                'required'        => false,
                'label'           => 'sylius.form.product_attribute.position',
                'invalid_message' => 'sylius.product_attribute.invalid',
            ]);
    }

    public function getBlockPrefix(): string
    {
        return 'umanit_product_variant_attribute';
    }
}

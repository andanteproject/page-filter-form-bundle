<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\Form;

use Andante\PageFilterFormBundle\Form\PageFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class DumbObjectPageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('child1', TextType::class, [
            'target_callback' => function (\stdClass $obj, ?string $a): void {
                $obj->criteriaSearch1 = $a;
            },
        ]);
        $builder->add('child2', TextType::class, [
            'target_callback' => function (\stdClass $obj, ?string $a, FormInterface $form): void {
                $obj->criteriaSearch2 = $a;
            },
        ]);
    }

    public function getParent()
    {
        return PageFilterType::class;
    }
}

<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\Form;

use Andante\PageFilterFormBundle\Form\PageFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class DumbArrayPageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('child1', TextType::class, [
            'target_callback' => function (array &$array, ?string $a): void {
                $array['criteriaSearch1'] = $a;
            },
        ]);
        $builder->add('child2', TextType::class, [
            'target_callback' => function (array &$array, ?string $a, FormInterface $form): void {
                $array['criteriaSearch2'] = $a;
            },
        ]);
    }

    public function getParent()
    {
        return PageFilterType::class;
    }
}

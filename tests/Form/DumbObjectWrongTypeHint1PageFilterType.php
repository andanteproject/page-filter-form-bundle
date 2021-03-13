<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\Form;

use Andante\PageFilterFormBundle\Form\PageFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DumbObjectWrongTypeHint1PageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('child1', TextType::class, [
            'target_callback' => function (array $obj, ?int $a): void {
            },
        ]);
    }

    public function getParent()
    {
        return PageFilterType::class;
    }
}

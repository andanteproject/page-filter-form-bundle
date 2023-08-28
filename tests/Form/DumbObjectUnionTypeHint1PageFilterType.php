<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\Form;

use Andante\PageFilterFormBundle\Form\PageFilterType;
use Andante\PageFilterFormBundle\Tests\Model\Test1Interface;
use Andante\PageFilterFormBundle\Tests\Model\Test2Interface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DumbObjectUnionTypeHint1PageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (\version_compare(PHP_VERSION, '8.0.0') >= 0) {
            $builder->add('child1', TextType::class, [
                'target_callback' => function (Test1Interface|Test2Interface $obj, ?int $a): void {
                },
            ]);
        }
    }

    public function getParent()
    {
        return PageFilterType::class;
    }
}

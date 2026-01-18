<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\Form;

use Andante\PageFilterFormBundle\Form\PageFilterType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DumbObjectWrongTypeHint2PageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('child1', TextType::class, [
            'target_callback' => function (\stdClass $obj, ?int $a): void {
                $obj->criteriaSearch1 = $a;
            },
        ]);
    }

    public function getParent(): ?string
    {
        return PageFilterType::class;
    }
}

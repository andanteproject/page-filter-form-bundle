<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\Form\Extension;

use Andante\PageFilterFormBundle\Tests\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class AndanteSmartFormAttrExtensionTest extends KernelTestCase
{
    public function testExtensionOff(): void
    {
        $builder = $this->getFormFactory()->createNamedBuilder('main', FormType::class);
        $builder->add('child1', FormType::class);
        $builder->add('child2', FormType::class);
        $form = $builder->getForm();
        $view = $form->createView();
        self::assertArrayNotHasKey('form', $view->children['child1']->vars['attr']);
        self::assertArrayNotHasKey('form', $view->children['child2']->vars['attr']);
    }

    public function testWithFormName(): void
    {
        $builder = $this->getFormFactory()->createNamedBuilder('main', FormType::class, null, [
            'andante_smart_form_attr' => true,
        ]);
        $builder->add('child1', FormType::class);
        $builder->add('child2', FormType::class);
        $form = $builder->getForm();
        $view = $form->createView();
        self::assertArrayHasKey('form', $view->children['child1']->vars['attr']);
        self::assertArrayHasKey('form', $view->children['child2']->vars['attr']);

        self::assertEquals('main', $view->children['child1']->vars['attr']['form']);
        self::assertEquals('main', $view->children['child2']->vars['attr']['form']);
    }

    public function testWithFormUnnamed(): void
    {
        $builder = $this->getFormFactory()->createNamedBuilder('', FormType::class, null, [
            'andante_smart_form_attr' => true,
        ]);
        $builder->add('child1', FormType::class);
        $builder->add('child2', FormType::class);
        $form = $builder->getForm();
        $view = $form->createView();
        self::assertArrayHasKey('form', $view->children['child1']->vars['attr']);
        self::assertArrayHasKey('form', $view->children['child2']->vars['attr']);

        self::assertNotEmpty($view->vars['attr']['id']);
        self::assertStringContainsString('form-', $view->vars['attr']['id']);
        self::assertEquals($view->vars['attr']['id'], $view->children['child1']->vars['attr']['form']);
        self::assertEquals($view->vars['attr']['id'], $view->children['child2']->vars['attr']['form']);
    }
}

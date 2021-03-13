<?php

namespace Andante\PageFilterFormBundle\Tests\Form\Extension;

use Andante\PageFilterFormBundle\Tests\KernelTestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class TargetCallbackExtensionTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testExtension(): void
    {
        $builder = $this->getFormFactory()->createNamedBuilder('main', FormType::class, null, [
            'target_callback' => fn (string $a): string => $a,
        ]);
        $form = $builder->getForm();
        self::assertIsCallable($form->getConfig()->getOption('target_callback'));
    }
}

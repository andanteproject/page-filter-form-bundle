<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests;

use Andante\PageFilterFormBundle\Tests\HttpKernel\AndantePageFilterFormKernel;
use Symfony\Component\Form\FormFactoryInterface;

class KernelTestCase extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    protected function getFormFactory(): FormFactoryInterface
    {
        $container = self::$container;
        /** @var FormFactoryInterface $formFactory */
        $formFactory = $container->get('form.factory');

        return $formFactory;
    }

    protected static function getKernelClass(): string
    {
        return AndantePageFilterFormKernel::class;
    }
}

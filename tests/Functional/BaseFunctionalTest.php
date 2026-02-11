<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\Functional;

use Andante\PageFilterFormBundle\PageFilterManagerInterface;
use Andante\PageFilterFormBundle\Tests\App\PageFilterFormAppKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

abstract class BaseFunctionalTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::ensureKernelShutdown();
    }

    protected static function getKernelClass(): string
    {
        return PageFilterFormAppKernel::class;
    }

    protected function getFormFactory(): FormFactoryInterface
    {
        $container = self::getContainer();
        /** @var FormFactoryInterface $formFactory */
        $formFactory = $container->get('form.factory');

        return $formFactory;
    }

    protected function getPageFilterManager(): PageFilterManagerInterface
    {
        $container = self::getContainer();
        /** @var PageFilterManagerInterface $manager */
        $manager = $container->get(PageFilterManagerInterface::class);

        return $manager;
    }
}

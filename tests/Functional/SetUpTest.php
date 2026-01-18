<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\Functional;

use Andante\PageFilterFormBundle\PageFilterManagerInterface;
use Andante\PageFilterFormBundle\Tests\KernelTestCase;
use Andante\PageFilterFormBundle\Tests\Services\DumbService;
use Symfony\Component\Form\FormFactoryInterface;

class SetUpTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testDependencyInjection(): void
    {
        /** @var DumbService $dumbService */
        $dumbService = self::getContainer()->get(DumbService::class);
        $rProperty = new \ReflectionProperty($dumbService, 'pageFilterManager');
        $filterManager = $rProperty->getValue($dumbService);
        self::assertInstanceOf(PageFilterManagerInterface::class, $filterManager);
        $rProperty = new \ReflectionProperty($filterManager, 'formFactory');
        self::assertInstanceOf(FormFactoryInterface::class, $rProperty->getValue($filterManager));
    }
}

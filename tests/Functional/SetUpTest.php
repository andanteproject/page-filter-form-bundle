<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\Functional;

use Andante\PageFilterFormBundle\PageFilterManagerInterface;
use Andante\PageFilterFormBundle\Tests\Form\DumbObjectPageFilterType;
use Andante\PageFilterFormBundle\Tests\Services\DumbService;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SetUpTest extends BaseFunctionalTest
{
    public function testDependencyInjection(): void
    {
        $container = self::getTestContainer();

        $filterManager = $container->get(PageFilterManagerInterface::class);
        self::assertInstanceOf(PageFilterManagerInterface::class, $filterManager);

        $dumbService = $container->get(DumbService::class);
        self::assertInstanceOf(DumbService::class, $dumbService);

        // Verify DumbService has a working PageFilterManager by using it
        $target = new \stdClass();
        $target->criteriaSearch1 = null;
        $target->criteriaSearch2 = null;
        $form = $dumbService->createAndHandleFilter(
            DumbObjectPageFilterType::class,
            $target,
            Request::create('/', 'GET', ['child1' => 'a', 'child2' => 'b'])
        );
        self::assertInstanceOf(FormInterface::class, $form);
        self::assertSame('a', $target->criteriaSearch1);
        self::assertSame('b', $target->criteriaSearch2);
    }
}

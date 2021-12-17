<?php

namespace Andante\PageFilterFormBundle\Tests\Functional;

use Andante\PageFilterFormBundle\Exception\TargetCallableArgumentException;
use Andante\PageFilterFormBundle\PageFilterManager;
use Andante\PageFilterFormBundle\PageFilterManagerInterface;
use Andante\PageFilterFormBundle\Tests\Form\DumbArrayByValuePageFilterType;
use Andante\PageFilterFormBundle\Tests\Form\DumbArrayPageFilterType;
use Andante\PageFilterFormBundle\Tests\Form\DumbObjectNotEnoughParametersPageFilterType;
use Andante\PageFilterFormBundle\Tests\Form\DumbObjectPageFilterType;
use Andante\PageFilterFormBundle\Tests\Form\DumbObjectWrongTypeHint1PageFilterType;
use Andante\PageFilterFormBundle\Tests\Form\DumbObjectWrongTypeHint2PageFilterType;
use Andante\PageFilterFormBundle\Tests\Form\DumbObjectWrongTypeHint3PageFilterType;
use Andante\PageFilterFormBundle\Tests\Form\DumbObjectWrongTypeHintNoNullableArgs2PageFilterType;
use Andante\PageFilterFormBundle\Tests\KernelTestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PageFilterManagerTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testCreateAndHandleFilterWithObject(): void
    {
        /** @var PageFilterManagerInterface $filterManager */
        $filterManager = self::getContainer()->get(PageFilterManager::class);
        $fakeQueryBuilder = new \stdClass();
        $fakeQueryBuilder->criteriaSearch1 = null;
        $fakeQueryBuilder->criteriaSearch2 = null;
        $form = $filterManager->createAndHandleFilter(
            DumbObjectPageFilterType::class,
            $fakeQueryBuilder,
            Request::create('/', 'GET', [
                'child1' => 'newCriteriaSearch1',
                'child2' => 'newCriteriaSearch2',
            ])
        );
        self::assertInstanceOf(FormInterface::class, $form);
        self::assertEquals('newCriteriaSearch1', $fakeQueryBuilder->criteriaSearch1);
        self::assertEquals('newCriteriaSearch2', $fakeQueryBuilder->criteriaSearch2);
    }

    public function testCreateAndHandleFilterWithObjectNullArgs(): void
    {
        /** @var PageFilterManagerInterface $filterManager */
        $filterManager = self::getContainer()->get(PageFilterManager::class);
        $fakeQueryBuilder = new \stdClass();
        $fakeQueryBuilder->criteriaSearch1 = null;
        $fakeQueryBuilder->criteriaSearch2 = null;
        $form = $filterManager->createAndHandleFilter(
            DumbObjectPageFilterType::class,
            $fakeQueryBuilder,
            Request::create('/', 'GET', [
                'child1' => null,
                'child2' => null,
            ])
        );
        self::assertInstanceOf(FormInterface::class, $form);
        self::assertEquals(null, $fakeQueryBuilder->criteriaSearch1);
        self::assertEquals(null, $fakeQueryBuilder->criteriaSearch2);
    }

    public function testCreateAndHandleFilterWithObjectNullArgsWithNoNullableArgs2(): void
    {
        /** @var PageFilterManagerInterface $filterManager */
        $filterManager = self::getContainer()->get(PageFilterManager::class);
        $fakeQueryBuilder = new \stdClass();
        $fakeQueryBuilder->criteriaSearch1 = null;
        $fakeQueryBuilder->criteriaSearch2 = null;
        $this->expectException(TargetCallableArgumentException::class);
        $this->expectExceptionMessage('must have second argument nullable');
        $filterManager->createAndHandleFilter(
            DumbObjectWrongTypeHintNoNullableArgs2PageFilterType::class,
            $fakeQueryBuilder,
            Request::create('/', 'GET', [
                'child1' => null,
                'child2' => null,
            ])
        );
    }

    public function testCreateAndHandleFilterWithObjectNotEnoughParameters(): void
    {
        /** @var PageFilterManagerInterface $filterManager */
        $filterManager = self::getContainer()->get(PageFilterManager::class);
        $fakeQueryBuilder = new \stdClass();
        $fakeQueryBuilder->criteriaSearch1 = null;
        $this->expectException(TargetCallableArgumentException::class);
        $this->expectExceptionMessage('at least 2 argument');
        $filterManager->createAndHandleFilter(
            DumbObjectNotEnoughParametersPageFilterType::class,
            $fakeQueryBuilder,
            Request::create('/', 'GET', [
                'child1' => 'newCriteriaSearch1',
            ])
        );
    }

    public function testCreateAndHandleFilterWithObjectWrongTypeHint1(): void
    {
        /** @var PageFilterManagerInterface $filterManager */
        $filterManager = self::getContainer()->get(PageFilterManager::class);
        $fakeQueryBuilder = new \stdClass();
        $fakeQueryBuilder->criteriaSearch1 = null;
        $this->expectException(TargetCallableArgumentException::class);
        $this->expectExceptionMessage('argument type-hinted');
        $filterManager->createAndHandleFilter(
            DumbObjectWrongTypeHint1PageFilterType::class,
            $fakeQueryBuilder,
            Request::create('/', 'GET', [
                'child1' => 'newCriteriaSearch1',
            ])
        );
    }

    public function testCreateAndHandleFilterWithObjectWrongTypeHint2(): void
    {
        /** @var PageFilterManagerInterface $filterManager */
        $filterManager = self::getContainer()->get(PageFilterManager::class);
        $fakeQueryBuilder = new \stdClass();
        $fakeQueryBuilder->criteriaSearch1 = null;
        $this->expectException(TargetCallableArgumentException::class);
        $this->expectExceptionMessage('argument type-hinted');
        $filterManager->createAndHandleFilter(
            DumbObjectWrongTypeHint2PageFilterType::class,
            $fakeQueryBuilder,
            Request::create('/', 'GET', [
                'child1' => 'newCriteriaSearch1',
            ])
        );
    }

    public function testCreateAndHandleFilterWithObjectWrongTypeHint3(): void
    {
        /** @var PageFilterManagerInterface $filterManager */
        $filterManager = self::getContainer()->get(PageFilterManager::class);
        $fakeQueryBuilder = new \stdClass();
        $fakeQueryBuilder->criteriaSearch1 = null;
        $this->expectException(TargetCallableArgumentException::class);
        $this->expectExceptionMessage(\sprintf('argument type-hinted as "%s"', FormInterface::class));
        $filterManager->createAndHandleFilter(
            DumbObjectWrongTypeHint3PageFilterType::class,
            $fakeQueryBuilder,
            Request::create('/', 'GET', [
                'child1' => 'newCriteriaSearch1',
            ])
        );
    }

    public function testCreateAndHandleFilterWithArray(): void
    {
        /** @var PageFilterManagerInterface $filterManager */
        $filterManager = self::getContainer()->get(PageFilterManager::class);
        $array = [
            'criteriaSearch1' => null,
            'criteriaSearch2' => null,
        ];
        $form = $filterManager->createAndHandleFilter(
            DumbArrayPageFilterType::class,
            $array,
            Request::create('/', 'GET', [
                'child1' => 'newCriteriaSearch1',
                'child2' => 'newCriteriaSearch2',
            ])
        );
        self::assertInstanceOf(FormInterface::class, $form);
        self::assertEquals('newCriteriaSearch1', $array['criteriaSearch1']);
        self::assertEquals('newCriteriaSearch2', $array['criteriaSearch2']);
    }

    public function testCreateAndHandleFilterWithArrayByValue(): void
    {
        /** @var PageFilterManagerInterface $filterManager */
        $filterManager = self::getContainer()->get(PageFilterManager::class);
        $array = [
            'criteriaSearch1' => null,
        ];
        $this->expectException(TargetCallableArgumentException::class);
        $this->expectExceptionMessage('reference');
        $filterManager->createAndHandleFilter(
            DumbArrayByValuePageFilterType::class,
            $array,
            Request::create('/', 'GET', [
                'child1' => 'newCriteriaSearch1',
            ])
        );
    }
}

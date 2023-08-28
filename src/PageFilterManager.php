<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle;

use Andante\PageFilterFormBundle\Form\Extension\TargetCallbackExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PageFilterManager implements PageFilterManagerInterface
{
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param mixed      $target
     * @param Request    $request
     * @param mixed|null $data
     */
    public function createAndHandleFilter(
        string $formType,
        &$target,
        Request $request = null,
        $data = null,
        array $options = [],
        string $formName = ''
    ): FormInterface {
        $form = $this->createFilter($formType, $data, $options, $formName);

        if (null !== $request) {
            $this->handleRequest($form, $request, $target);
        } else {
            $this->applyFilterTargetCallbacks($form, $target);
        }

        return $form;
    }

    /**
     * @param mixed                $data
     * @param array<string, mixed> $options
     */
    public function createFilter(
        string $formType,
        $data = null,
        array $options = [],
        string $formName = ''
    ): FormInterface {
        $formBuilder = $this->formFactory->createNamedBuilder(
            $formName,
            $formType,
            $data,
            $options
        );

        return $formBuilder->getForm();
    }

    /**
     * @param mixed $target
     */
    public function handleRequest(FormInterface $form, Request $request, &$target): void
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->applyFilterTargetCallbacks($form, $target);
        }
    }

    /**
     * @param mixed $target
     */
    public function applyFilterTargetCallbacks(FormInterface $form, &$target): void
    {
        /** @var callable|null $targetCallback */
        $targetCallback = $form->getConfig()->getOption(TargetCallbackExtension::NAME);
        if (null !== $targetCallback) {
            if (!\is_object($target)) {
                Assert::assertTargetCallableHasArrayTargetArgumentAsReference($targetCallback);
            }
            $formData = $form->getData();
            Assert::assertTargetCallableHasRightTypeHintedArguments($targetCallback, $target, $formData);
            $targetCallback($target, $formData, $form);
        }
        foreach ($form->all() as $child) {
            $this->applyFilterTargetCallbacks($child, $target);
        }
    }
}

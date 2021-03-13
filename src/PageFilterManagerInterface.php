<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface PageFilterManagerInterface
{
    /**
     * @param mixed                $target
     * @param mixed|null           $data
     * @param array<string, mixed> $options
     * @param ?Request             $request
     */
    public function createAndHandleFilter(
        string $formType,
        &$target,
        ?Request $request = null,
        $data = null,
        array $options = [],
        string $formName = ''
    ): FormInterface;

    /**
     * @param mixed|null $target
     */
    public function applyFilterTargetCallbacks(FormInterface $form, &$target): void;
}

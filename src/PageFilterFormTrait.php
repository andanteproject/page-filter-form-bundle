<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\Attribute\Required;

trait PageFilterFormTrait
{
    protected PageFilterManagerInterface $pageFilterManager;

    /**
     * @param mixed           $target
     * @param mixed|null           $data
     * @param array<string, mixed> $options
     */
    public function createAndHandleFilter(
        string $formType,
        &$target,
        Request $request,
        $data = null,
        array $options = [],
        string $formName = ''
    ): FormInterface {
        return $this->pageFilterManager->createAndHandleFilter(
            $formType,
            $target,
            $request,
            $data,
            $options,
            $formName
        );
    }

    #[Required]
    public function setPageFilterManager(PageFilterManagerInterface $pageFilterManager): void
    {
        $this->pageFilterManager = $pageFilterManager;
    }
}

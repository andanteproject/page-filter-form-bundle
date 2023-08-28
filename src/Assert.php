<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle;

use Andante\PageFilterFormBundle\Exception\TargetCallableArgumentException;
use Andante\PageFilterFormBundle\Form\Extension\TargetCallbackExtension;
use Symfony\Component\Form\FormInterface;

class Assert
{
    public static function assertTargetCallableHasArrayTargetArgumentAsReference(callable $targetCallback): void
    {
        // @phpstan-ignore-next-line
        $r = new \ReflectionFunction($targetCallback);
        self::assertFunctionHasAtLeast2Arguments($r);
        $params = $r->getParameters();
        /** @var \ReflectionParameter $firstParam */
        $firstParam = $params[0];
        if (!$firstParam->isPassedByReference()) {
            throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" is used for a target of type array. You MUST provide first callable argument as reference by prefixing parameter name with "&"', TargetCallbackExtension::NAME));
        }
    }

    public static function assertFunctionHasAtLeast2Arguments(\ReflectionFunction $function): void
    {
        if (\count($function->getParameters()) < 2) {
            throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" must have at least 2 argument: $target and $formData', TargetCallbackExtension::NAME));
        }
    }

    /**
     * @param mixed $target
     * @param mixed $formData
     */
    public static function assertTargetCallableHasRightTypeHintedArguments(
        callable $targetCallback,
        $target,
        $formData
    ): void {
        // @phpstan-ignore-next-line
        $r = new \ReflectionFunction($targetCallback);
        self::assertFunctionHasAtLeast2Arguments($r);
        $params = $r->getParameters();
        $targetParam = $params[0];
        $formDataParam = $params[1];
        $formParam = $params[2] ?? null;

        // Checking $target argument
        if (null === $target && !$targetParam->allowsNull()) {
            throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" must have first argument nullable', TargetCallbackExtension::NAME));
        }

        $targetParamClass = $targetParam->getType() && !$targetParam->getType()->isBuiltin() ? new \ReflectionClass($targetParam->getType()->getName()) : null;
        if (
            null !== $target
            && $targetParam->hasType()
            && (null !== $targetParamClass ?
                !\is_a($target, $targetParamClass->getName(), true) :
                // @phpstan-ignore-next-line
                ($targetParamType = $targetParam->getType()) !== null && \get_debug_type($target) !== $targetParamType->getName())) {
            throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" must have first argument type-hinted as "%s"', TargetCallbackExtension::NAME, \get_debug_type($target)));
        }

        // Checking $formData argument
        if (null === $formData && !$formDataParam->allowsNull()) {
            throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" must have second argument nullable', TargetCallbackExtension::NAME));
        }

        $formDataParamClass = $formDataParam->getType() && !$formDataParam->getType()->isBuiltin() ? new \ReflectionClass($formDataParam->getType()->getName()) : null;
        if (
            null !== $formData
            && $formDataParam->hasType()
            && (null !== $formDataParamClass ?
                !\is_a($formData, $formDataParamClass->getName(), true) :
                // @phpstan-ignore-next-line
                ($formDataParamType = $formDataParam->getType()) !== null && \get_debug_type($formData) !== $formDataParamType->getName())) {
            throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" must have second argument type-hinted as "%s"', TargetCallbackExtension::NAME, \get_debug_type($formData)));
        }

        // Checking $form argument
        if (null !== $formParam) {
            $formParamClass = $formParam->getType() && !$formParam->getType()->isBuiltin() ? new \ReflectionClass($formParam->getType()->getName()) : null;
            if (
                $formParam->hasType()
                && (
                    null === $formParamClass
                    || !\is_a($formParamClass->getName(), FormInterface::class, true)
                )
            ) {
                throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" must have third argument type-hinted as "%s"', TargetCallbackExtension::NAME, FormInterface::class));
            }
        }
    }
}

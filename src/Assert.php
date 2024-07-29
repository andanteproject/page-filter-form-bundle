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

        if (!self::callableAcceptsValue($r, 0, $target)) {
            throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" must have first argument type-hinted as "%s"', TargetCallbackExtension::NAME, \get_debug_type($target)));
        }

        // Checking $formData argument
        if (null === $formData && !$formDataParam->allowsNull()) {
            throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" must have second argument nullable', TargetCallbackExtension::NAME));
        }

        if (!self::callableAcceptsValue($r, 1, $formData)) {
            throw new TargetCallableArgumentException(\sprintf('Callable for option "%s" must have second argument type-hinted as "%s"', TargetCallbackExtension::NAME, \get_debug_type($formData)));
        }

        // Checking $form argument
        if (null !== $formParam) {
            $formParamClass = null;
            $formParamType = $formParam->getType();
            if ($formParamType instanceof \ReflectionNamedType) {
                /** @var class-string<object> $formParamTypeName */
                $formParamTypeName = $formParamType->getName();
                $formParamClass = !$formParamType->isBuiltin() ? new \ReflectionClass($formParamTypeName) : null;
            }
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

    /**
     * @param mixed $paramValue
     */
    protected static function callableAcceptsValue(\ReflectionFunction $r, int $paramPosition, $paramValue): bool
    {
        foreach ($r->getParameters() as $parameter) {
            if ($parameter->getPosition() !== $paramPosition) {
                continue;
            }

            $type = $parameter->getType();
            if ($type instanceof \ReflectionNamedType) {
                return self::isCompatibleWithType($type->getName(), $paramValue) || ($type->allowsNull() && \is_null($paramValue));
            }

            if (\version_compare(PHP_VERSION, '8.0.0') >= 0 && $type instanceof \ReflectionUnionType) {
                foreach ($type->getTypes() as $t) {
                    if($t instanceof \ReflectionNamedType) {
                        if (self::isCompatibleWithType($t->getName(), $paramValue) || ($t->allowsNull() && \is_null($paramValue))) {
                            return true;
                        }
                    }
                }

                return false;
            }

            if (\version_compare(PHP_VERSION, '8.1.0') >= 0 && $type instanceof \ReflectionIntersectionType) {
                foreach ($type->getTypes() as $t) {
                    if($t instanceof \ReflectionNamedType) {
                        if (!self::isCompatibleWithType($t->getName(), $paramValue)) { /* intersection types do not support nullables for now */
                            return false;
                        }
                    }
                }

                return true;
            }

            return false;
        }

        return false;
    }

    /**
     * @param mixed|object $value
     */
    protected static function isCompatibleWithType(string $type, $value): bool
    {
        if (\is_object($value)) {
            // @phpstan-ignore-next-line
            return \get_class($value) === $type || \is_subclass_of($value, $type);
        }

        $gettype = \gettype($value);

        if ('boolean' === $gettype) {
            return 'bool' === $type;
        }

        if ('integer' === $gettype) {
            return 'int' === $type;
        }

        return $gettype === $type;
    }
}

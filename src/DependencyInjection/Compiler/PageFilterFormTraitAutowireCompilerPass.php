<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\DependencyInjection\Compiler;

use Andante\PageFilterFormBundle\PageFilterManagerInterface;
use Andante\PageFilterFormBundle\PageFilterFormTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Workaround to inject PageFilterManager into services on Symfony 8 with PHP 8+
 * so we don't have to drop PHP 7 compatibility. This pass runs only when PHP >= 8.
 *
 * The trait uses @required (docblock) instead of #[Required] (attribute) because
 * attributes require PHP 8.0+; on PHP 7.4 @required is enough. On PHP 8+ with
 * Symfony 8, method injection from traits is not always applied, so this pass
 * adds the setter call when needed.
 */
final class PageFilterFormTraitAutowireCompilerPass implements CompilerPassInterface
{
    private const TRAIT_NAME = PageFilterFormTrait::class;

    public function process(ContainerBuilder $container): void
    {
        $reference = new Reference(PageFilterManagerInterface::class);

        foreach ($container->getDefinitions() as $definition) {
            if ($definition->isAbstract()) {
                continue;
            }

            $class = $definition->getClass();
            if ($class === null) {
                continue;
            }

            try {
                $reflection = $container->getReflectionClass($class, false);
            } catch (\Throwable $e) {
                continue;
            }

            if ($reflection === null || !$reflection->hasMethod('setPageFilterManager')) {
                continue;
            }

            if (!$this->usesTrait($reflection)) {
                continue;
            }

            foreach ($definition->getMethodCalls() as $call) {
                if (($call[0] ?? '') === 'setPageFilterManager') {
                    continue 2;
                }
            }

            $definition->addMethodCall('setPageFilterManager', [$reference]);
        }
    }

    /**
     * @param \ReflectionClass<object> $class
     */
    private function usesTrait(\ReflectionClass $class): bool
    {
        if (\in_array(self::TRAIT_NAME, $class->getTraitNames(), true)) {
            return true;
        }

        foreach ($class->getTraitNames() as $usedTrait) {
            try {
                if ($this->usesTrait(new \ReflectionClass($usedTrait))) {
                    return true;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        $parent = $class->getParentClass();
        return $parent instanceof \ReflectionClass && $this->usesTrait($parent);
    }
}

<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle;

use Andante\PageFilterFormBundle\DependencyInjection\Compiler\PageFilterFormTraitAutowireCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AndantePageFilterFormBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        if (\PHP_VERSION_ID >= 80000) {
            $container->addCompilerPass(new PageFilterFormTraitAutowireCompilerPass());
        }
    }
}

<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\App;

use Andante\PageFilterFormBundle\AndantePageFilterFormBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

abstract class AppKernel extends Kernel
{
    /** @var array<string, array<string, mixed>> */
    protected array $config = [];

    /**
     * @param array<string, array<string, mixed>> $config
     */
    public function __construct(string $environment, bool $debug, array $config = [])
    {
        parent::__construct($environment, $debug);
        $this->config = $config;
    }

    /**
     * @return iterable<int, Bundle>
     */
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new AndantePageFilterFormBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.php');

        if (Kernel::VERSION_ID >= 60100) {
            $loader->load(function (ContainerBuilder $container): void {
                $frameworkConfig = [
                    'http_method_override' => false,
                ];
                if (Kernel::VERSION_ID >= 60400) {
                    $frameworkConfig['handle_all_throwables'] = true;
                    $frameworkConfig['php_errors'] = ['log' => true];
                }
                $container->loadFromExtension('framework', $frameworkConfig);
            });
        }

        if (\count($this->config) > 0) {
            $loader->load(function (ContainerBuilder $container): void {
                foreach ($this->config as $extension => $config) {
                    $container->loadFromExtension($extension, $config);
                }
            });
        }
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }
}

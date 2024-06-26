<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\HttpKernel;

use Andante\PageFilterFormBundle\AndantePageFilterFormBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AndantePageFilterFormKernel extends Kernel
{
    private array $configs = [];

    public function registerBundles(): array
    {
        return [
            new AndantePageFilterFormBundle(),
            new FrameworkBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config_test.php');

        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    public function getCacheDir(): string
    {
        return \sprintf(__DIR__.'/../../var/cache/test/%s/', \hash('crc32b', (string) \json_encode($this->configs)));
    }

    public function getLogDir(): string
    {
        return __DIR__.'/../../var/logs/test/';
    }

    public function setConfigs(array $configs): self
    {
        $this->configs = $configs;

        return $this;
    }

    public function addConfig(string $configPath, bool $addKernelDirPrefix = true): self
    {
        $configPath = $addKernelDirPrefix ? __DIR__.$configPath : $configPath;
        $this->configs[] = $configPath;

        return $this;
    }
}

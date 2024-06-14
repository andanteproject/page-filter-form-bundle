<?php

declare(strict_types=1);

use Andante\PageFilterFormBundle\PageFilterManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set('kernel.secret', 'secret')
        ->set('locale', 'en');

    $containerConfigurator->extension('framework', [
        'test' => true,
    ]);

    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->public()
        ->autoconfigure();

    // makes classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services->load('Andante\PageFilterFormBundle\Tests\Services\\', __DIR__.'/../../Services/*');

    $services->set(PageFilterManager::class)
        ->public();
};

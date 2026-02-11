<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()
        ->set('kernel.secret', 'test_secret')
        ->set('locale', 'en');

    $containerConfigurator->extension('framework', [
        'secret' => '%kernel.secret%',
        'test' => true,
    ]);

    $services = $containerConfigurator->services();
    $services->defaults()
        ->autowire()
        ->public()
        ->autoconfigure();

    $services->load('Andante\PageFilterFormBundle\Tests\Services\\', __DIR__.'/../../Services/*');
};

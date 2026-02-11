<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\DependencyInjection;

use Andante\PageFilterFormBundle\Form\Extension\AndanteSmartFormAttrExtension;
use Andante\PageFilterFormBundle\Form\Extension\TargetCallbackExtension;
use Andante\PageFilterFormBundle\PageFilterManager;
use Andante\PageFilterFormBundle\PageFilterManagerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

class AndantePageFilterFormExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $filterManagerDefinition = $container->setDefinition(
            'andante_page_filter_form.page_filter_manager',
            new Definition(PageFilterManager::class)
        );
        $filterManagerDefinition->addArgument(new Reference('form.factory'));
        $container->setAlias(PageFilterManagerInterface::class, 'andante_page_filter_form.page_filter_manager');

        $container
            ->setDefinition(
                'andante_page_filter_form.smart_form_attr_extension',
                new Definition(AndanteSmartFormAttrExtension::class)
            )
            ->addTag('form.type_extension');
        $container
            ->setDefinition(
                'andante_page_filter_form.target_callback_extension',
                new Definition(TargetCallbackExtension::class)
            )
            ->addTag('form.type_extension');
    }
}

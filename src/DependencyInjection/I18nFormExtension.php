<?php

namespace Koff\Bundle\I18nFormBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class I18nFormExtension.
 */
class I18nFormExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader->load('services.xml');

        $localeProvider = $container->getDefinition('koff_i18n_form.locale_provider');
        $localeProvider->replaceArgument(0, $config['locales']);
        $localeProvider->replaceArgument(1, $container->getParameter('kernel.default_locale'));
        $localeProvider->replaceArgument(2, $config['required_locales']);

        $formManipulator = $container->getDefinition('koff_i18n_form.form_manipulator');
        $formManipulator->replaceArgument(1, $config['excluded_fields']);
    }
}

<?php

namespace Koff\I18nFormBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class I18nFormExtension.
 */
class KoffI18nFormExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/../config'));
        $loader->load('services.xml');

        $this->defineLocaleProvider($config, $container);
        $this->defineFormManipulator($config, $container);
        if (array_key_exists('form_theme', $config)) {
            $this->defineTemplate($config, $container);
        }
    }

    private function defineLocaleProvider(array $config, ContainerBuilder $container)
    {
        $localeProvider = $container->getDefinition('koff_i18n_form.locale_provider');
        $localeProvider->replaceArgument(0, $config['locales']);
        $localeProvider->replaceArgument(1, $container->getParameter('kernel.default_locale'));
        $localeProvider->replaceArgument(2, $config['required_locales']);
    }

    private function defineFormManipulator(array $config, ContainerBuilder $container)
    {
        $formManipulator = $container->getDefinition('koff_i18n_form.form_manipulator');
        $formManipulator->replaceArgument(1, $config['excluded_fields']);
    }

    private function defineTemplate(array $config, ContainerBuilder $container)
    {
        $twigFormResources = $container->getParameter('twig.form.resources');
        $isFormThemeRegistered = false;

        array_walk(
            $twigFormResources,
            function ($value) use (&$isFormThemeRegistered) {
                $isFormThemeRegistered = false !== stripos($value, '@KoffI18nForm/');
            }
        );

        if (!$isFormThemeRegistered) {
            $twigFormResources[] = '@KoffI18nForm/'.$config['form_theme'].'_form.html.twig';
            $container->setParameter('twig.form.resources', $twigFormResources);
        }
    }
}

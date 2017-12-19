<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 * (c) Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koff\Bundle\I18nFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KoffI18nFormExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('a2lix_form.xml');

        $container->setParameter('koff_i18n_form.locale_provider', $config['locale_provider']);
        $container->setParameter('koff_i18n_form.locales', $config['locales']);
        $container->setParameter('koff_i18n_form.required_locales', $config['required_locales']);
        $container->setParameter('koff_i18n_form.default_locale', $config['default_locale'] ?: $container->getParameter('kernel.default_locale', 'en'));

        $container->setParameter('koff_i18n_form.templating', $config['templating']);

        $defaultManipulator = $container->getDefinition('koff_i18n_form.default.manipulator');
        $defaultManipulator->replaceArgument(1, $config['excluded_fields']);
    }
}

<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koff\Bundle\I18nFormBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LocaleProviderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $localeProvider = $container->getParameter('koff_i18n_form.locale_provider');

        if ('default' !== $localeProvider) {
            $container->setAlias('koff_i18n_form.default.service.locale_provider', $localeProvider);

            return;
        }

        $container->setAlias(
            'koff_i18n_form.default.service.locale_provider',
            'koff_i18n_form.default.service.parameter_locale_provider'
        );

        $definition = $container->getDefinition('koff_i18n_form.default.service.parameter_locale_provider');
        $definition->setArguments(
            [
                $container->getParameter('koff_i18n_form.locales'),
                $container->getParameter('koff_i18n_form.default_locale'),
                $container->getParameter('koff_i18n_form.required_locales'),
            ]
        );
    }
}

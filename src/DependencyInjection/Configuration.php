<?php

namespace Koff\Bundle\I18nFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @author David ALLIX
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('koff_i18n_form')->children();

        $convertStringToArray = function ($v) {
            return preg_split('/\s*[,|]\s*/', $v);
        };

        $locales = $root->arrayNode('locales');
        $locales->defaultValue(['en']);
        $locales->beforeNormalization()->ifString()->then($convertStringToArray);
        $locales->requiresAtLeastOneElement();
        $locales->prototype('scalar');

        $required = $root->arrayNode('required_locales');
        $required->beforeNormalization()->ifString()->then($convertStringToArray);
        $required->prototype('scalar');

        $excluded = $root->arrayNode('excluded_fields');
        $excluded->defaultValue(['id', 'locale', 'translatable']);
        $excluded->beforeNormalization()->ifString()->then($convertStringToArray);
        $excluded->prototype('scalar');

        return $treeBuilder;
    }
}

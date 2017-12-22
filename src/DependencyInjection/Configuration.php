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
        $rootNode = $treeBuilder->root('koff_i18n_form');

        $rootNode
            ->children()
                ->arrayNode('locales')
                    ->defaultValue(['en'])
                    ->beforeNormalization()
                        ->ifString()->then(function ($v) {return preg_split('/\s*[,|]\s*/', $v); })
                    ->end()
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('required_locales')
                    ->beforeNormalization()
                        ->ifString()->then(function ($v) {return preg_split('/\s*[,|]\s*/', $v); })
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('excluded_fields')
                    ->defaultValue(['id', 'locale', 'translatable'])
                    ->beforeNormalization()
                        ->ifString()->then(function ($v) {return preg_split('/\s*[,|]\s*/', $v); })
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

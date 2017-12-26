<?php

namespace Koff\Bundle\I18nFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    private $convertStringToArray = null;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('i18n_form');

        $this->convertStringToArray = function ($v) {
            return preg_split('/\s*[,|]\s*/', $v);
        };

        $this->localesSection($rootNode);
        $this->requiredLocalesSection($rootNode);
        $this->excludedFieldsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function localesSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('locales')
                    ->defaultValue(['en'])
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')
                    ->beforeNormalization()
                        ->ifString()->then($this->convertStringToArray)
                    ->end()
                ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function requiredLocalesSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('required_locales')
                    ->prototype('scalar')
                    ->beforeNormalization()
                        ->ifString()->then($this->convertStringToArray)
                    ->end()
                ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function excludedFieldsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('locales')
                    ->defaultValue(['id', 'locale', 'translatable'])
                    ->prototype('scalar')
                    ->beforeNormalization()
                        ->ifString()->then($this->convertStringToArray)
                    ->end()
                ->end()
        ;
    }
}

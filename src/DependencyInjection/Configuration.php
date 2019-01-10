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
    private $convertStringToArray;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('i18n_form');
        $rootNode = method_exists($treeBuilder, 'getRootNode')
            ? $treeBuilder->getRootNode()
            : $treeBuilder->root('i18n_form');

        $this->convertStringToArray = function ($v) {
            return preg_split('/\s*[,|]\s*/', $v);
        };

        $this->localesSection($rootNode);
        $this->requiredLocalesSection($rootNode);
        $this->excludedFieldsSection($rootNode);
        $this->formThemeSection($rootNode);

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
                    ->scalarPrototype()->end()
                    ->beforeNormalization()->ifString()->then($this->convertStringToArray)->end()
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
                    ->scalarPrototype()->end()
                    ->beforeNormalization()->ifString()->then($this->convertStringToArray)->end()
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
                ->arrayNode('excluded_fields')
                    ->defaultValue(['id', 'locale', 'translatable'])
                    ->scalarPrototype()->end()
                    ->beforeNormalization()->ifString()->then($this->convertStringToArray)->end()
                ->end()
        ;
    }

    private function formThemeSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->scalarNode('form_theme')
                    ->validate()->ifNotInArray(['bootstrap_3', 'bootstrap_4'])->thenUnset()->end()
                ->end()
        ;
    }
}

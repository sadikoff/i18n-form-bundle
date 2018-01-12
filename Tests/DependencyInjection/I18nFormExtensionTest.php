<?php

namespace Koff\Bundle\I18nFormBundle\Tests\DependencyInjection;

use Koff\Bundle\I18nFormBundle\DependencyInjection\I18nFormExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * Class I18nFormExtensionTest
 *
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
class I18nFormExtensionTest extends TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    protected function tearDown()
    {
        unset($this->configuration);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessLocaleIsEmpty()
    {
        $loader = new I18nFormExtension();
        $config = $this->getDefaultConfig();
        $config['i18n_form']['locales'] = [];
        $loader->load($config, $this->getTestContainer());
    }

    public function testDefineLocaleProvider()
    {
        $configuration = $this->getTestContainer();
        $loader = new I18nFormExtension();
        $config = $this->getDefaultConfig();
        $loader->load($config, $configuration);

        $this->assertTrue(($configuration->hasDefinition('koff_i18n_form.locale_provider') ?: $configuration->hasAlias('koff_i18n_form.locale_provider')));
    }

    public function testDefineFormManipulator()
    {
        $configuration = $this->getTestContainer();
        $loader = new I18nFormExtension();
        $config = $this->getDefaultConfig();
        $loader->load($config, $configuration);

        $this->assertTrue(($configuration->hasDefinition('koff_i18n_form.form_manipulator') ?: $configuration->hasAlias('koff_i18n_form.form_manipulator')));
    }

    /**
     * getEmptyConfig.
     *
     * @return array
     */
    private function getDefaultConfig()
    {
        $yaml = <<<EOF
i18n_form:
    locales: 'en|de'
    required_locales: 'en'
    form_theme: 'bootstrap_4'
EOF;
        $parser = new Parser();
        return $parser->parse($yaml);
    }

    private function getTestContainer()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.default_locale', 'en');
        $container->setParameter('twig.form.resources', []);

        return $container;
    }
}

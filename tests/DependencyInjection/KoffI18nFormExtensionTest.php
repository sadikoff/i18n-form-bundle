<?php

namespace Koff\Bundle\I18nFormBundle\Tests\DependencyInjection;

use Koff\Bundle\I18nFormBundle\DependencyInjection\KoffI18NFormExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * Class I18nFormExtensionTest
 *
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
class KoffI18nFormExtensionTest extends TestCase
{
    /** @var ContainerBuilder */
    protected $configuration;

    protected function tearDown(): void
    {
        unset($this->configuration);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessLocaleIsEmpty()
    {
        $loader = new KoffI18NFormExtension();
        $config = $this->getDefaultConfig();
        $config['i18n_form']['locales'] = [];
        $loader->load($config, $this->getTestContainer());
    }

    public function testDefineLocaleProvider()
    {
        $configuration = $this->getTestContainer();
        $loader = new KoffI18NFormExtension();
        $config = $this->getDefaultConfig();
        $loader->load($config, $configuration);

        $this->assertTrue(($configuration->hasDefinition('koff_i18n_form.locale_provider') ?: $configuration->hasAlias('koff_i18n_form.locale_provider')));
    }

    public function testDefineFormManipulator()
    {
        $configuration = $this->getTestContainer();
        $loader = new KoffI18NFormExtension();
        $config = $this->getDefaultConfig();
        $loader->load($config, $configuration);

        $this->assertTrue(($configuration->hasDefinition('koff_i18n_form.form_manipulator') ?: $configuration->hasAlias('koff_i18n_form.form_manipulator')));
    }

    public function testDefaultConfgiLoad()
    {
        $configuration = $this->getTestContainer();
        $loader = new KoffI18NFormExtension();
        $config = $this->getDefaultConfig();
        $loader->load($config, $configuration);

        $registeredResources = $configuration->getParameter('twig.form.resources');
        $this->assertEquals(['@KoffI18nForm/bootstrap_4_form.html.twig'], $registeredResources);
    }

    public function testDefaultConfgiLoadWithTwigPredefined()
    {
        $configuration = $this->getTestContainerWithTwigResource();
        $loader = new KoffI18NFormExtension();
        $config = $this->getDefaultConfig();
        $loader->load($config, $configuration);

        $registeredResources = $configuration->getParameter('twig.form.resources');
        $this->assertEquals(['@KoffI18nForm/bootstrap_4_form.html.twig'], $registeredResources);
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

    private function getTestContainerWithTwigResource()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.default_locale', 'en');
        $container->setParameter('twig.form.resources', [
            '@KoffI18nForm/bootstrap_4_form.html.twig'
        ]);

        return $container;
    }
}

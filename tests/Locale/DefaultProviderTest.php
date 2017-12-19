<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koff\Bundle\I18nFormBundle\Tests\Locale;

use PHPUnit\Framework\TestCase;
use Koff\Bundle\I18nFormBundle\Locale\DefaultProvider;

class DefaultProviderTest extends TestCase
{
    protected $locales;
    protected $defaultLocale;
    protected $requiredLocales;
    protected $provider;

    public function setUp()
    {
        $this->locales = ['es', 'en', 'pt'];
        $this->defaultLocale = 'en';
        $this->requiredLocales = ['es', 'en'];

        $this->provider = new DefaultProvider($this->locales, $this->defaultLocale, $this->requiredLocales);
    }

    public function testDefaultLocaleIsInLocales()
    {
        $classname = 'Koff\Bundle\I18nFormBundle\Locale\DefaultProvider';

        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->getMock();

        // set expectations for constructor calls
        $this->expectException(
            'InvalidArgumentException', 'Default locale `de` not found within the configured locales `[es,en]`.'
                . ' Perhaps you need to add it to your `koff_i18n_form.locales` bundle configuration?'
        );

        // now call the constructor
        $reflectedClass = new \ReflectionClass($classname);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, ['es', 'en'], 'de', []);
    }

    public function testRequiredLocaleAreInLocales()
    {
        $classname = 'Koff\Bundle\I18nFormBundle\Locale\DefaultProvider';

        // Get mock, without the constructor being called
        $mock = $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->getMock();

        // set expectations for constructor calls
        $this->expectException(
            'InvalidArgumentException', 'Required locales should be contained in locales'
        );

        // now call the constructor
        $reflectedClass = new \ReflectionClass($classname);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($mock, ['es', 'en'], 'en', ['en', 'pt']);
    }

    public function testGetLocales()
    {
        $expected = $this->provider->getLocales();
        $locales = $this->locales;

        $this->assertSame(array_diff($expected, $locales), array_diff($locales, $expected));
    }

    public function testGetDefaultLocale()
    {
        $expected = $this->provider->getDefaultLocale();

        $this->assertSame($this->defaultLocale, $expected);
    }

    public function getRequiredLocales()
    {
        $expected = $this->provider->getDefaultLocale();
        $requiredLocales = $this->requiredLocales;

        $this->assertSame(array_diff($expected, $requiredLocales), array_diff($requiredLocales, $expected));
    }
}

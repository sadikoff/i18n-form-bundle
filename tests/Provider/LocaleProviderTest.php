<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koff\I18nFormBundle\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Koff\I18nFormBundle\Provider\LocaleProvider;

class LocaleProviderTest extends TestCase
{
    /** @var array */
    protected $locales;
    /** @var string */
    protected $defaultLocale;
    /** @var array */
    protected $requiredLocales;
    /** @var LocaleProvider */
    protected $provider;

    public function setUp()
    {
        $this->locales = ['es', 'en', 'pt'];
        $this->defaultLocale = 'en';
        $this->requiredLocales = ['es', 'en'];

        $this->provider = new LocaleProvider($this->locales, $this->defaultLocale, $this->requiredLocales);
    }

    public function testIsLocalesConfigured()
    {
        // set expectations for constructor calls
        $this->expectException('InvalidArgumentException');

        // now call the constructor
        $reflectedClass = new \ReflectionClass('Koff\\I18nFormBundle\\Provider\\LocaleProvider');
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($this->getLocaleProviderMock(), [], 'de', []);
    }

    public function testDefaultLocaleIsInLocales()
    {
        // set expectations for constructor calls
        $this->expectException('InvalidArgumentException');

        // now call the constructor
        $reflectedClass = new \ReflectionClass('Koff\\I18nFormBundle\\Provider\\LocaleProvider');
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($this->getLocaleProviderMock(), ['es', 'en'], 'de', []);
    }

    public function testRequiredLocaleAreInLocales()
    {
        // set expectations for constructor calls
        $this->expectException('InvalidArgumentException');

        // now call the constructor
        $reflectedClass = new \ReflectionClass('Koff\\I18nFormBundle\\Provider\\LocaleProvider');
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($this->getLocaleProviderMock(), ['es', 'en'], 'en', ['en', 'pt']);
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

    public function testGetRequiredLocales()
    {
        $expected = $this->provider->getRequiredLocales();
        $requiredLocales = $this->requiredLocales;

        $this->assertSame(array_diff($expected, $requiredLocales), array_diff($requiredLocales, $expected));
    }

    private function getLocaleProviderMock()
    {
        return $this->getMockBuilder('Koff\\I18nFormBundle\\Provider\\LocaleProvider')
            ->disableOriginalConstructor()
            ->getMock();
    }
}

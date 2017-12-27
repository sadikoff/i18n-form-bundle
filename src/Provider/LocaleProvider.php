<?php

namespace Koff\Bundle\I18nFormBundle\Provider;

/**
 * Class LocaleProvider.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 */
class LocaleProvider implements LocaleProviderInterface
{
    /** @var array */
    protected $locales;

    /** @var string */
    protected $defaultLocale;

    /** @var array */
    protected $requiredLocales;

    /**
     * @param array  $locales
     * @param string $defaultLocale
     * @param array  $requiredLocales
     */
    public function __construct(array $locales, $defaultLocale, array $requiredLocales = [])
    {
        if (!in_array($defaultLocale, $locales, true)) {
            if (count($locales)) {
                throw new \InvalidArgumentException(sprintf('Default locale `%s` not found within the configured locales `[%s]`. Perhaps you need to add it to your `i18n_form.locales` bundle configuration?', $defaultLocale, implode(',', $locales)));
            }

            throw new \InvalidArgumentException(sprintf('No locales were configured, but expected at least the default locale `%s`. Perhaps you need to add it to your `i18n_form.locales` bundle configuration?', $defaultLocale));
        }

        if (array_diff($requiredLocales, $locales)) {
            throw new \InvalidArgumentException('Required locales should be contained in locales');
        }

        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
        $this->requiredLocales = $requiredLocales;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredLocales()
    {
        return $this->requiredLocales;
    }
}

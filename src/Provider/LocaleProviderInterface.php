<?php

namespace Koff\Bundle\I18nFormBundle\Provider;

/**
 * Interface LocaleProviderInterface.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 */
interface LocaleProviderInterface
{
    /**
     * Get array of locales.
     */
    public function getLocales(): array;

    /**
     * Get default locale.
     */
    public function getDefaultLocale(): string;

    /**
     * Get required locales.
     */
    public function getRequiredLocales(): array;
}

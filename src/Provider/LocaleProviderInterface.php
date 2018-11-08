<?php

namespace Koff\I18nFormBundle\Provider;

/**
 * Interface LocaleProviderInterface.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 */
interface LocaleProviderInterface
{
    /**
     * Get array of locales.
     *
     * @return array
     */
    public function getLocales();

    /**
     * Get default locale.
     *
     * @return string
     */
    public function getDefaultLocale();

    /**
     * Get required locales.
     *
     * @return array
     */
    public function getRequiredLocales();
}

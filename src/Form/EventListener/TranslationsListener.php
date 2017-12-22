<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koff\Bundle\I18nFormBundle\Form\EventListener;

use Koff\Bundle\I18nFormBundle\Form\Manipulator\FormManipulatorInterface;
use Koff\Bundle\I18nFormBundle\Form\Type\AutoFormType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class TranslationsListener extends KoffI18nListener
{
    /** @var FormManipulatorInterface */
    private $formManipulator;

    /**
     * @param FormManipulatorInterface $formManipulator
     */
    public function __construct(FormManipulatorInterface $formManipulator)
    {
        $this->formManipulator = $formManipulator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => 'submit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $formOptions = $form->getConfig()->getOptions();

        $fieldsOptions = $this->getFieldsOptions($form, $formOptions);
        $translationClass = $this->getTranslationClass($form->getParent());

        foreach ($formOptions['locales'] as $locale) {
            if (isset($fieldsOptions[$locale])) {
                $form->add($locale, AutoFormType::class, [
                    'data_class' => $translationClass,
                    'required' => in_array($locale, $formOptions['required_locales'], true),
                    'block_name' => ('field' === $formOptions['theming_granularity']) ? 'locale' : null,
                    'fields' => $fieldsOptions[$locale],
                    'excluded_fields' => $formOptions['excluded_fields'],
                ]);
            }
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    private function getTranslationClass(FormInterface $form)
    {
        do {
            $translatableClass = $form->getConfig()->getDataClass();
        } while ((null === $translatableClass) && $form->getConfig()->getVirtual() && ($form = $form->getParent()));

        // Knp
        if (method_exists($translatableClass, 'getTranslationEntityClass')) {
            return $translatableClass::getTranslationEntityClass();
        }

        // Gedmo
        if (method_exists($translatableClass, 'getTranslationClass')) {
            return $translatableClass::getTranslationClass();
        }

        return $translatableClass.'Translation';
    }

    /**
     * @param FormInterface $form
     * @param array         $formOptions
     *
     * @return array
     */
    public function getFieldsOptions(FormInterface $form, array $formOptions)
    {
        $fieldsOptions = [];
        $fieldsConfig = $this->formManipulator->getFieldsConfig($form);

        foreach ($fieldsConfig as $field => $config) {
            $localeOptions = $this->extractLocaleOptions($config);

            foreach ($formOptions['locales'] as $locale) {
                $fieldsOptions[$locale][$field] = (false !== $localeOptions && array_key_exists($locale, $localeOptions)) ? ($localeOptions[$locale] + $config) : $config;
            }
        }

        return $fieldsOptions;
    }

    /**
     * @param $config
     *
     * @return array|bool
     */
    private function extractLocaleOptions(&$config)
    {
        if (array_key_exists('locale_options', $config)) {
            $localeOptions = $config['locale_options'];
            unset($config['locale_options']);

            return $localeOptions;
        }

        return false;
    }
}

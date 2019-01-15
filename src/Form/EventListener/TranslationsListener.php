<?php

namespace Koff\I18nFormBundle\Form\EventListener;

use Koff\I18nFormBundle\Form\Manipulator\FormManipulatorInterface;
use Koff\I18nFormBundle\Form\Type\AutoFormType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

/**
 * Class TranslationsListener.
 *
 * @author David ALLIX
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
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
                    'required' => \in_array($locale, $formOptions['required_locales'], true),
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
    private function getTranslationClass(FormInterface $form): string
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
    public function getFieldsOptions(FormInterface $form, array $formOptions): array
    {
        $fieldsOptions = array_fill_keys($formOptions['locales'], $this->formManipulator->getFieldsConfig($form));

        foreach ($fieldsOptions as $locale => &$field) {
            array_walk($field, function (&$v, $k, $l) {
                if (array_key_exists('locale_options', $v) && array_key_exists($l, $v['locale_options'])) {
                    $lo = $v['locale_options'];
                    unset($v['locale_options']);

                    $v = $lo[$l] + $v;
                }
            }, $locale);
        }

        return $fieldsOptions;
    }
}

<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koff\I18nFormBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;

/**
 * Class TranslationsFormsListener.
 *
 * @author David ALLIX
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
class TranslationsFormsListener extends KoffI18nListener
{
    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $formOptions = $form->getConfig()->getOptions();

        foreach ($formOptions['locales'] as $locale) {
            $form->add(
                $locale,
                $formOptions['form_type'],
                $formOptions['form_options'] + [
                    'required' => in_array($locale, $formOptions['required_locales'], true),
                ]
            );
        }
    }
}

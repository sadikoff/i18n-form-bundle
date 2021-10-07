<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 * (c) Gonzalo Vilaseca <gvilaseca@reiss.co.uk> . Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koff\Bundle\I18nFormBundle\Form\Type;

use Koff\Bundle\I18nFormBundle\Form\EventListener\TranslationsFormsListener;
use Koff\Bundle\I18nFormBundle\Provider\LocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsFormsType extends AbstractType
{
    /** @var TranslationsFormsListener */
    private $translationsListener;

    /** @var LocaleProviderInterface */
    private $localeProvider;

    public function __construct(TranslationsFormsListener $translationsListener, LocaleProviderInterface $localeProvider)
    {
        $this->translationsListener = $translationsListener;
        $this->localeProvider = $localeProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->translationsListener);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['default_locale'] = $options['default_locale'];
        $view->vars['required_locales'] = $options['required_locales'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'by_reference' => false,
            'empty_data' => function () {
                return new \Doctrine\Common\Collections\ArrayCollection();
            },
            'locales' => $this->localeProvider->getLocales(),
            'default_locale' => $this->localeProvider->getDefaultLocale(),
            'required_locales' => $this->localeProvider->getRequiredLocales(),
            'form_type' => null,
            'form_options' => [],
        ]);

        $resolver->setNormalizer('form_options', function (Options $options, $value) {
            // Check mandatory data_class option when AutoFormType use
            if (is_a($options['form_type'], AutoFormType::class, true) && !isset($value['data_class'])) {
                throw new \RuntimeException('Missing "data_class" option under "form_options" of TranslationsFormsType. Required when "form_type" use "AutoFormType".');
            }

            return $value;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'koff_i18n_forms';
    }
}

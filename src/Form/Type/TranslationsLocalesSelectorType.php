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

use Koff\Bundle\I18nFormBundle\Provider\LocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationsLocalesSelectorType extends AbstractType
{
    /** @var LocaleProviderInterface */
    private $localeProvider;

    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['default_locale'] = $this->localeProvider->getDefaultLocale();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_combine($this->localeProvider->getLocales(), $this->localeProvider->getLocales()),
            'expanded' => true,
            'multiple' => true,
            'attr' => [
                'class' => 'a2lix_translationsLocalesSelector',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'a2lix_translationsLocalesSelector';
    }
}

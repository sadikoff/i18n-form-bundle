<?php

namespace Koff\Bundle\I18nFormBundle\Form\Type;

use Koff\Bundle\I18nFormBundle\Form\EventListener\AutoFormListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AutoFormType extends AbstractType
{
    /** @var autoFormListener */
    private $autoFormListener;

    public function __construct(AutoFormListener $autoFormListener)
    {
        $this->autoFormListener = $autoFormListener;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->autoFormListener);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['fields' => [], 'excluded_fields' => []]);

        $resolver->setNormalizer(
            'data_class',
            function (Options $options, $value) {
                if (empty($value)) {
                    throw new \RuntimeException('Missing "data_class" option of "AutoFormType".');
                }

                return $value;
            }
        );
    }
}

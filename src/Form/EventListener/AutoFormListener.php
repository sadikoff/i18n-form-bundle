<?php

namespace Koff\Bundle\I18nFormBundle\Form\EventListener;

use Koff\Bundle\I18nFormBundle\Form\Manipulator\FormManipulatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AutoFormListener implements EventSubscriberInterface
{
    /** @var FormManipulatorInterface */
    private $formManipulator;

    public function __construct(FormManipulatorInterface $formManipulator)
    {
        $this->formManipulator = $formManipulator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();

        $fieldsOptions = $this->formManipulator->getFieldsConfig($form);
        foreach ($fieldsOptions as $fieldName => $fieldConfig) {
            $fieldType = $fieldConfig['field_type'] ?? null;
            unset($fieldConfig['field_type']);

            $form->add($fieldName, $fieldType, $fieldConfig);
        }
    }
}

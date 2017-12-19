<?php

namespace Koff\Bundle\I18nFormBundle\Form\Manipulator;

use Symfony\Component\Form\FormInterface;

interface FormManipulatorInterface
{
    /**
     * @param FormInterface $form
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function getFieldsConfig(FormInterface $form);
}

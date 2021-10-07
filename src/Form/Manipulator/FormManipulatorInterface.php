<?php

namespace Koff\Bundle\I18nFormBundle\Form\Manipulator;

use Symfony\Component\Form\FormInterface;

interface FormManipulatorInterface
{
    /**
     * @throws \RuntimeException
     */
    public function getFieldsConfig(FormInterface $form): array;
}

<?php

namespace Koff\Bundle\I18nFormBundle\Form\Manipulator;

use Koff\Bundle\I18nFormBundle\ObjectInfo\ObjectInfoInterface;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Form\FormInterface;

class DefaultManipulator implements FormManipulatorInterface
{
    /** @var ObjectInfoInterface */
    private $objectInfo;
    /** @var array */
    private $globalExcludedFields;

    /**
     * @param ObjectInfoInterface $objectInfo
     * @param array               $globalExcludedFields
     */
    public function __construct(ObjectInfoInterface $objectInfo, array $globalExcludedFields = [])
    {
        $this->objectInfo = $objectInfo;
        $this->globalExcludedFields = $globalExcludedFields;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsConfig(FormInterface $form)
    {
        $class = $this->getDataClass($form);
        $formOptions = $form->getConfig()->getOptions();

        $formFields = $formOptions['fields'];

        $objectFields = $this->objectInfo->getFieldsConfig($class);
        $objectFields = $this->filteringUsuableFields($objectFields, $formOptions['excluded_fields']);

        if (empty($formFields)) {
            return $objectFields;
        }

        $this->checkUnknownFields(array_keys($formFields), array_keys($objectFields), $class);

        $fieldsConfig = [];

        foreach ($formFields as $fieldName => $fieldConfig) {
            if (null !== $fieldConfig && (!isset($fieldConfig['display']) || (false !== $fieldConfig['display']))) {
                $fieldsConfig[$fieldName] = $fieldConfig + $objectFields[$fieldName];
            }
        }

        return $fieldsConfig;
    }

    /**
     * @param array  $formFields
     * @param array  $objectFields
     * @param string $class
     *
     * @throws \RuntimeException
     */
    private function checkUnknownFields($formFields, $objectFields, $class)
    {
        $unknowsFields = array_diff($formFields, $objectFields);
        if (!empty($unknowsFields)) {
            throw new \RuntimeException(
                sprintf("Field(s) '%s' doesn't exist in %s", implode(', ', $unknowsFields), $class)
            );
        }
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    private function getDataClass(FormInterface $form)
    {
        // Simple case, data_class from current form
        if ($dataClass = $form->getConfig()->getDataClass()) {
            return ClassUtils::getRealClass($dataClass);
        }

        // Advanced case, loop parent form to get closest fill data_class
        while ($formParent = $form->getParent()) {
            if (!$dataClass = $formParent->getConfig()->getDataClass()) {
                $form = $formParent;
                continue;
            }

            return $this->objectInfo->getAssociationTargetClass($dataClass, $form->getName());
        }
    }

    /**
     * @param array $objectFieldsConfig
     * @param array $formExcludedFields
     *
     * @return array
     */
    private function filteringUsuableFields(array $objectFieldsConfig, array $formExcludedFields)
    {
        $excludedFields = array_merge($this->globalExcludedFields, $formExcludedFields);

        $usualableFields = [];
        foreach ($objectFieldsConfig as $fieldName => $fieldConfig) {
            if (in_array($fieldName, $excludedFields, true)) {
                continue;
            }

            $usualableFields[$fieldName] = $fieldConfig;
        }

        return $usualableFields;
    }
}

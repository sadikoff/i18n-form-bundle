<?php

namespace Koff\Bundle\I18nFormBundle\Form\Manipulator;

use Doctrine\Common\Util\ClassUtils;
use Koff\Bundle\I18nFormBundle\ObjectInfo\ObjectInfoInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class DefaultManipulator.
 *
 * @author David ALLIX
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
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
        $objectFields = $this->filterObjectFields($objectFields, $formOptions['excluded_fields']);

        if (empty($formFields)) {
            return $objectFields;
        }

        $this->checkUnknownFields($formFields, $objectFields, $class);

        $fieldsConfig = $this->filterFields($formFields);
        array_walk($fieldsConfig, function (&$v, $k, $d) {
            $v += $d[$k];
        }, $objectFields);

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
        $unknowsFields = array_keys(array_diff_key($formFields, $objectFields));
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
    private function filterObjectFields(array $objectFieldsConfig, array $formExcludedFields)
    {
        $excludedFields = array_fill_keys(array_merge($this->globalExcludedFields, $formExcludedFields), []);

        return array_diff_key($objectFieldsConfig, $excludedFields);
    }

    private function filterFields($fields)
    {
        return array_filter($fields, function ($v) {
            return !(null === $v || (array_key_exists('display', $v) && !$v['display']));
        });
    }
}

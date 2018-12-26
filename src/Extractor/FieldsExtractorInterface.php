<?php

namespace Koff\I18nFormBundle\Extractor;

/**
 * Interface EntityInfoInterface.
 *
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
interface FieldsExtractorInterface
{
    public function getFieldsConfig($class);

    public function getAssociationTargetClass(string $class, string $fieldName): string;
}

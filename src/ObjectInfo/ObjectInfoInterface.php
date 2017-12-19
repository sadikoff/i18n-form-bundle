<?php

namespace Koff\Bundle\I18nFormBundle\ObjectInfo;


interface ObjectInfoInterface
{
    public function getFieldsConfig($class);

    public function getAssociationTargetClass($class, $fieldName);
}

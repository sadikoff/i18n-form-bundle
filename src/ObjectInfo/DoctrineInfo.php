<?php

namespace Koff\Bundle\I18nFormBundle\ObjectInfo;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Koff\Bundle\I18nFormBundle\Form\Type\AutoFormType;

class DoctrineInfo implements ObjectInfoInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $class
     *
     * @return array
     */
    public function getFieldsConfig($class)
    {
        $fieldsConfig = [];

        $metadata = $this->entityManager->getClassMetadata($class);

        if ($fields = $metadata->getFieldNames()) {
            $fieldsConfig = array_fill_keys($fields, []);
        }

        if ($assocNames = $metadata->getAssociationNames()) {
            $fieldsConfig += $this->getAssocsConfig($metadata, $assocNames);
        }

        return $fieldsConfig;
    }

    /**
     * @param ClassMetadata $metadata
     * @param array         $assocNames
     *
     * @return array
     */
    private function getAssocsConfig(ClassMetadata $metadata, $assocNames)
    {
        $assocsConfigs = [];

        foreach ($assocNames as $assocName) {
            if (!$metadata->isAssociationInverseSide($assocName)) {
                continue;
            }

            $class = $metadata->getAssociationTargetClass($assocName);

            if ($metadata->isSingleValuedAssociation($assocName)) {
                $nullable = ($metadata instanceof ClassMetadataInfo) && isset($metadata->discriminatorColumn['nullable']) && $metadata->discriminatorColumn['nullable'];

                $assocsConfigs[$assocName] = [
                    'field_type' => AutoFormType::class,
                    'data_class' => $class,
                    'required' => !$nullable,
                ];

                continue;
            }

            $assocsConfigs[$assocName] = [
                'field_type' => 'Symfony\Component\Form\Extension\Core\Type\CollectionType',
                'entry_type' => AutoFormType::class,
                'entry_options' => [
                    'data_class' => $class,
                ],
                'allow_add' => true,
                'by_reference' => false,
            ];
        }

        return $assocsConfigs;
    }

    /**
     * @param string $class
     * @param string $fieldName
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getAssociationTargetClass($class, $fieldName)
    {
        $metadata = $this->entityManager->getClassMetadata($class);

        if (!$metadata->hasAssociation($fieldName)) {
            throw new \Exception(
                sprintf('Unable to find the association target class of "%s" in %s.', $fieldName, $class)
            );
        }

        return $metadata->getAssociationTargetClass($fieldName);
    }
}

<?php

namespace Koff\Bundle\I18nFormBundle\Extractor;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Koff\Bundle\I18nFormBundle\Form\Type\AutoFormType;

/**
 * Class DoctrineEntityFieldsExtractor.
 *
 * @author David ALLIX
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
class DoctrineEntityFieldsExtractor implements FieldsExtractorInterface
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
            if ($metadata->isAssociationInverseSide($assocName)) {
                $class = $metadata->getAssociationTargetClass($assocName);

                $assocsConfigs[$assocName] = $this->generateConfig($class, $metadata, $assocName);
            }
        }

        return $assocsConfigs;
    }

    /**
     * @param string        $class
     * @param ClassMetadata $metadata
     * @param string        $assocName
     *
     * @return array
     */
    private function generateConfig($class, ClassMetadata $metadata, $assocName)
    {
        if ($metadata->isSingleValuedAssociation($assocName)) {
            return [
                'field_type' => AutoFormType::class,
                'data_class' => $class,
                'required' => !(array_key_exists('nullable', $metadata->discriminatorColumn) && $metadata->discriminatorColumn['nullable']),
            ];
        }

        return [
            'field_type' => 'Symfony\Component\Form\Extension\Core\Type\CollectionType',
            'entry_type' => AutoFormType::class,
            'entry_options' => [
                'data_class' => $class,
            ],
            'allow_add' => true,
            'by_reference' => false,
        ];
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

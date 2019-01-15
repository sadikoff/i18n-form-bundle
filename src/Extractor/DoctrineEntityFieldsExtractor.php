<?php

namespace Koff\I18nFormBundle\Extractor;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Koff\I18nFormBundle\Form\Type\AutoFormType;

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
    public function getFieldsConfig($class): array
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
    private function getAssocsConfig(ClassMetadata $metadata, $assocNames): array
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

    private function generateConfig(string $class, ClassMetadata $metadata, string $assocName): array
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

    public function getAssociationTargetClass(string $class, string $fieldName): string
    {
        $metadata = $this->entityManager->getClassMetadata($class);

        if (!$metadata->hasAssociation($fieldName)) {
            //TODO: Create Customized Exception
            throw new \Exception(
                sprintf('Unable to find the association target class of "%s" in %s.', $fieldName, $class)
            );
        }

        return $metadata->getAssociationTargetClass($fieldName);
    }
}

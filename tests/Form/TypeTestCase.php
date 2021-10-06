<?php

/*
 * This file is part of A2lix projects.
 *
 * (c) David ALLIX
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Koff\Bundle\I18nFormBundle\Tests\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase as BaseTypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class TypeTestCase extends BaseTypeTestCase
{
    /** @var \Koff\Bundle\I18nFormBundle\Form\Manipulator\FormManipulator */
    protected $defaultFormManipulator;

    protected function setUp(): void
    {
        parent::setUp();

        $validator = $this->createMock('\Symfony\Component\Validator\Validator\ValidatorInterface');
        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(
                new FormTypeValidatorExtension($validator)
            )
            ->addTypeGuesser(
                $this->getMockBuilder('Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser')
                     ->disableOriginalConstructor()
                     ->getMock()
            )
            ->getFormFactory();

        $this->dispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    protected function getDefaultFormManipulator()
    {
        if (null !== $this->defaultFormManipulator) {
            return $this->defaultFormManipulator;
        }

        $config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/../Fixtures/Entity'], true, null, null, false);
        $entityManager = EntityManager::create(['driver' => 'pdo_sqlite'], $config);
        $doctrineInfo = new \Koff\Bundle\I18nFormBundle\Extractor\DoctrineEntityFieldsExtractor($entityManager);

        return $this->defaultFormManipulator = new \Koff\Bundle\I18nFormBundle\Form\Manipulator\FormManipulator($doctrineInfo, ['id', 'locale', 'translatable']);
    }

    protected function getConfiguredAutoFormType()
    {
        $AutoFormListener = new \Koff\Bundle\I18nFormBundle\Form\EventListener\AutoFormListener($this->getDefaultFormManipulator());

        return new \Koff\Bundle\I18nFormBundle\Form\Type\AutoFormType($AutoFormListener);
    }

    protected function getConfiguredTranslationsType($locales, $defaultLocale, $requiredLocales)
    {
        $translationsListener = new \Koff\Bundle\I18nFormBundle\Form\EventListener\TranslationsListener($this->getDefaultFormManipulator());
        $localProvider = new \Koff\Bundle\I18nFormBundle\Provider\LocaleProvider($locales, $defaultLocale, $requiredLocales);

        return new \Koff\Bundle\I18nFormBundle\Form\Type\TranslationsType($translationsListener, $localProvider);
    }

    protected function getConfiguredTranslationsFormsType($locales, $defaultLocale, $requiredLocales)
    {
        $translationsFormsListener = new \Koff\Bundle\I18nFormBundle\Form\EventListener\TranslationsFormsListener();
        $localProvider = new \Koff\Bundle\I18nFormBundle\Provider\LocaleProvider($locales, $defaultLocale, $requiredLocales);

        return new \Koff\Bundle\I18nFormBundle\Form\Type\TranslationsFormsType($translationsFormsListener, $localProvider);
    }
}

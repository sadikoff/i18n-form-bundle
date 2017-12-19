KoffI18nFormBundle
==================

[![Build Status](https://travis-ci.org/sadikoff/i18n-form-bundle.svg?branch=master)](https://travis-ci.org/sadikoff/i18n-form-bundle)
[![Latest Stable Version](https://poser.pugx.org/koff/i18n-form-bundle/v/stable.svg)](https://packagist.org/packages/koff/i18n-form-bundle) 
[![Total Downloads](https://poser.pugx.org/koff/i18n-form-bundle/downloads.svg)](https://packagist.org/packages/koff/i18n-form-bundle) 
[![Latest Unstable Version](https://poser.pugx.org/koff/i18n-form-bundle/v/unstable.svg)](https://packagist.org/packages/koff/i18n-form-bundle) 
[![License](https://poser.pugx.org/koff/i18n-form-bundle/license.svg)](https://packagist.org/packages/koff/i18n-form-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/sadikoff/i18n-form-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/sadikoff/i18n-form-bundle/?branch=master)

>Warning! Still in development but in stable state. 

>This bundle is fork of [a2lix/TranslationFormBundle](https://github.com/a2lix/TranslationFormBundle), reorganized and optimized to work with symfony flex 

Requirements
------------
* Symfony flex with Symfony 3.4|4.0
* i18n Doctrine strategy of your choice
  * [KnpDoctrineExtension](https://github.com/KnpLabs/DoctrineBehaviors#translatable)
  * [A2lixI18nDoctrineBundle](https://github.com/a2lix/I18nDoctrineBundle)
  * [PrezentDoctrineTranslatableBundle](https://github.com/Prezent/doctrine-translatable-bundle/blob/master/Resources/doc/index.md)

Installation
------------

    composer req koff/i18n-form-bundle

Configuration
-------------
Full configuration example

```yaml
# config/packages/koff_i18n_form.yaml
koff_i18n_form:
    locale_provider: default
    locales: [en, fr, es, de]
    default_locale: en
    required_locales: [fr]
```

Usage
-----

Basic example

```php
use Koff\I18nFormBundle\Form\Type\TranslationsType;
//...
$builder->add('translations', TranslationsType::class);
```

Advanced example

```php
use Koff\I18nFormBundle\Form\Type\TranslationsType;
//...
$builder->add('translations', TranslationsType::class, [
    'locales' => ['en', 'fr', 'es', 'de'],          // [1]
    'default_locale' => ['en']                      // [1]
    'required_locales' => ['fr'],                   // [1]
    'fields' => [                                   // [2]
        'description' => [                          // [3.a]
            'field_type' => 'textarea',             // [4]
            'label' => 'descript.',                 // [4]
            'locale_options' => [                   // [3.b]
                'es' => ['label' => 'descripción']  // [4]
                'fr' => ['display' => false]        // [4]
            ]
        ]
    ],
    'excluded_fields' => ['details']                // [2]
]);
```

* [1] Optionnal. If set, override the default value from config.yml
* [2] Optionnal. If set, override the default value from config.yml
* [3] Optionnal. If set, override the auto configuration of fields
* [3.a] Optionnal. - For a field, applied to all locales
* [3.b] Optionnal. - For a specific locale of a field
* [4] Optionnal. Common options of symfony forms (max_length, required, trim, read_only, constraints, ...), which was added 'field_type' and 'display'


Additional
----------

###### TranslationsFormsType

A different approach for entities which don't share fields untranslated. No strategy used here, only a locale field in your entity.

```php
use Koff\I18nFormBundle\Form\Type\TranslationsFormsType;
//...
$builder->add('translations', TranslationsFormsType::class, [
    'locales' => ['en', 'fr', 'es', 'de'],   // [1]
    'default_locale' => ['en']               // [1]
    'required_locales' => ['fr'],            // [1]
    'form_type' => ProductMediaType::class,  // [2 - Mandatory]
    'form_options' => [                      // [2bis]
        'context' => 'pdf'
    ]
]);
```

* [1] Optionnal. If set, override the default value from config.yml
* [2 - Mandatory]. A real form type that you have to do
* [2bis] Optionnal. - An array of options that you can set to your form

###### TranslatedEntityType

Modified version of the native 'entity' symfony2 form type to translate the label in the current locale by reading translations

```php
use Koff\I18nFormBundle\Form\Type\TranslatedEntityType;
//...
$builder->add('medias', TranslatedEntityType::class, [
    'class' => 'App\Entity\Media',      // [1 - Mandatory]
    'translation_property' => 'text',   // [2 - Mandatory]
    'multiple' => true,                 // [3]
]);
```
    
* [1] Path of the translatable class
* [2] Property/Method of the translatable class that will be display
* [3] Common options of the 'entity' symfony2 form type (multiple, ...)

###### Assets

If you already use Twitter Bootstrap, you only need to enable the Tab functionality and use `a2lix_translation_bootstrap.js`.

Otherwise, you will still need jquery, and you use `a2lix_translation_default.js` and `a2lix_translation_default.css`.

Credits
=======
All credits goes to [David ALLIX](https://github.com/a2lix) and his [a2lix/TranslationFormBundle](https://github.com/a2lix/TranslationFormBundle)

License
=======
This package is available under the MIT license.
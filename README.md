[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/abacaphiliac/zend-transformer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/abacaphiliac/zend-transformer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/abacaphiliac/zend-transformer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/abacaphiliac/zend-transformer/?branch=master)
[![Build Status](https://travis-ci.org/abacaphiliac/zend-transformer.svg?branch=master)](https://travis-ci.org/abacaphiliac/zend-transformer)

# abacaphiliac/zend-transformer
An object-to-object ETL package, based on Zend Framework extraction, hydration, and validation abstractions.

Includes a PluginManager for registering transformation specs via application config, and a ZF2 module to wire up all configuration.

Requires >=php55, and supports ZF2 but not ZF3 at this time.

# Installation
```bash
composer require abacaphiliac/zend-transformer
```

# Usage

Register transformers in your application config:

```php
return [
    'abacaphiliac/zend-transformer' => [
        'transformers' => [
            'SimpleFooBarToFizBuz' => [
                'inputClass' => \AbacaphiliacTest\FooBar::class,
                'keyMap' => [
                    'foo' => 'fiz',
                    'bar' => 'buz',
                ],
                'outputClass' => \AbacaphiliacTest\FizBuz::class,
            ],
        ],
    ],
];
```

Transform some data!

```php
$transformers = $serviceLocator->get('TransformerManager');
$transformer = $transformers->get('SimpleFooBarToFizBuz');

$input = new \AbacaphiliacTest\FooBar('Foo', 'Bar');
$output = $transformer->transform($input, \AbacaphiliacTest\FizBuz::class);
```

Complex configuration:

```php
return [
    'abacaphiliac/zend-transformer' => [
        'transformers' => [
            'ComplexFooBarToFizBuz' => [
                'input_validator' => 'MyInputValidatorFromValidatorManager',
                'extractor' => 'MyExractorFromHydratorManager',
                'transformer' => 'MyTransformerFromServiceManager',
                'hydrator' => 'MyHydratorFromHydratorManager',
                'output_validator' => 'MyOutputValidatorFromValidatorManager',
            ],
        ],
    ],
    'service_manager' => [
        'invokables' => [
            'MyTransformerFromServiceManager' => function (array $data) {
                // Don't do this in production, as the config cannot be cached.
                return [];
            },
        ],
    ],
    'validators' => [
        'invokables' => [
            'MyInputValidatorFromValidatorManager' => \Zend\Validator\ValidatorChain::class,
            'MyOutputValidatorFromValidatorManager' => \Zend\Validator\ValidatorChain::class,
        ],
    ],
    'hydrators' => [
        'invokables' => [
            'MyExractorFromHydratorManager' => \Zend\Hydrator\ClassMethods::class,
            'MyHydratorFromHydratorManager' => \Zend\Hydrator\ClassMethods::class,
        ],
    ],
];
```

## Contributing
```
composer update && vendor/bin/phpunit
```

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

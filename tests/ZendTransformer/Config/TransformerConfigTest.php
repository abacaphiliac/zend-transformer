<?php

namespace AbacaphiliacTest\Zend\Transformer\Config;

use Abacaphiliac\Zend\Transformer\Config\TransformerConfig;
use Abacaphiliac\Zend\Transformer\TransformerInterface;
use AbacaphiliacTest\FizBuz;
use AbacaphiliacTest\FooBar;
use Zend\Hydrator\ClassMethods;
use Zend\Validator\ValidatorChain;

/**
 * @covers \Abacaphiliac\Zend\Transformer\Config\TransformerConfig
 */
class TransformerConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromSnakeCaseOptions()
    {
        $config = new TransformerConfig([
            'input_class' => FooBar::class,
            'input_validator' => ValidatorChain::class,
            'extractor' => ClassMethods::class,
            'keyMap' => [
                'foo' => 'fiz',
                'bar' => 'buz',
            ],
            'transformer' => TransformerInterface::class,
            'hydrator' => ClassMethods::class,
            'output_class' => FizBuz::class,
            'output_validator' => ValidatorChain::class,
        ]);
        
        self::assertEquals(FooBar::class, $config->getInputClass());
        self::assertEquals(ValidatorChain::class, $config->getInputValidator());
        self::assertEquals(ClassMethods::class, $config->getExtractor());
        self::assertEquals(['foo' => 'fiz', 'bar' => 'buz'], $config->getKeyMap());
        self::assertEquals(TransformerInterface::class, $config->getTransformer());
        self::assertEquals(ClassMethods::class, $config->getHydrator());
        self::assertEquals(FizBuz::class, $config->getOutputClass());
        self::assertEquals(ValidatorChain::class, $config->getOutputValidator());
    }
    
    public function testCreateFromCamelCaseOptions()
    {
        $config = new TransformerConfig([
            'inputClass' => FooBar::class,
            'inputValidator' => ValidatorChain::class,
            'extractor' => ClassMethods::class,
            'keyMap' => [
                'foo' => 'fiz',
                'bar' => 'buz',
            ],
            'transformer' => TransformerInterface::class,
            'hydrator' => ClassMethods::class,
            'outputClass' => FizBuz::class,
            'outputValidator' => ValidatorChain::class,
        ]);
        
        self::assertEquals(FooBar::class, $config->getInputClass());
        self::assertEquals(ValidatorChain::class, $config->getInputValidator());
        self::assertEquals(ClassMethods::class, $config->getExtractor());
        self::assertEquals(['foo' => 'fiz', 'bar' => 'buz'], $config->getKeyMap());
        self::assertEquals(TransformerInterface::class, $config->getTransformer());
        self::assertEquals(ClassMethods::class, $config->getHydrator());
        self::assertEquals(FizBuz::class, $config->getOutputClass());
        self::assertEquals(ValidatorChain::class, $config->getOutputValidator());
    }
}

<?php

namespace AbacaphiliacTest\Zend\Transformer\Factory;

use Abacaphiliac\Zend\Transformer\Factory\AbstractTransformerFactory;
use Abacaphiliac\Zend\Transformer\TransformerInterface;
use AbacaphiliacTest\FizBuz;
use AbacaphiliacTest\FooBar;
use Zend\ServiceManager\ServiceManager;

class AbstractTransformerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ServiceManager */
    private $container;
    
    protected function setUp()
    {
        $this->container = new ServiceManager();
    }
    
    public function testTransformFromSimpleSpec()
    {
        $this->container->setService('config', [
            'abacaphiliac/zend-transformer' => [
                'transformers' => [
                    'FooBarToFizBuz' => [
                        'inputClass' => FooBar::class,
                        'keyMap' => [
                            'foo' => 'fiz',
                            'bar' => 'buz',
                        ],
                        'outputClass' => FizBuz::class,
                    ],
                ],
            ],
        ]);
        
        $sut = new AbstractTransformerFactory();
        self::assertTrue($sut->canCreate($this->container, 'FooBarToFizBuz'));
        
        $transformer = $sut($this->container, 'FooBarToFizBuz');
        self::assertInstanceOf(TransformerInterface::class, $transformer);
        
        $result = $transformer->transform(new FooBar('Alice', 'Bob'), new FizBuz());
        self::assertInstanceOf(FizBuz::class, $result);
        
        /** @var FizBuz $result */
        self::assertEquals('Alice', $result->getFiz());
        self::assertEquals('Bob', $result->getBuz());
    }
    
    /**
     * @expectedException \Abacaphiliac\Zend\Transformer\Exception\ValidationException
     */
    public function testNotTransformDueToInvalidInputClass()
    {
        $this->container->setService('config', [
            'abacaphiliac/zend-transformer' => [
                'transformers' => [
                    'FooBarToFizBuz' => [
                        'inputClass' => FizBuz::class,
                        'keyMap' => [
                            'foo' => 'fiz',
                            'bar' => 'buz',
                        ],
                        'outputClass' => FooBar::class,
                    ],
                ],
            ],
        ]);
        
        $sut = new AbstractTransformerFactory();
        self::assertTrue($sut->canCreate($this->container, 'FooBarToFizBuz'));
        
        $transformer = $sut($this->container, 'FooBarToFizBuz');
        self::assertInstanceOf(TransformerInterface::class, $transformer);
        
        $transformer->transform(new FooBar('Alice', 'Bob'), new FizBuz());
    }
    
    /**
     * @expectedException \Abacaphiliac\Zend\Transformer\Exception\ValidationException
     */
    public function testNotTransformDueToInvalidOutputClass()
    {
        $this->container->setService('config', [
            'abacaphiliac/zend-transformer' => [
                'transformers' => [
                    'FooBarToFizBuz' => [
                        'inputClass' => FooBar::class,
                        'keyMap' => [
                            'foo' => 'fiz',
                            'bar' => 'buz',
                        ],
                        'outputClass' => FooBar::class,
                    ],
                ],
            ],
        ]);
        
        $sut = new AbstractTransformerFactory();
        self::assertTrue($sut->canCreate($this->container, 'FooBarToFizBuz'));
        
        $transformer = $sut($this->container, 'FooBarToFizBuz');
        self::assertInstanceOf(TransformerInterface::class, $transformer);
        
        $transformer->transform(new FooBar('Alice', 'Bob'), new FizBuz());
    }
}

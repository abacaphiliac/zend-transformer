<?php

namespace AbacaphiliacTest\Zend\Transformer\Factory;

use Abacaphiliac\Zend\Transformer\Factory\AbstractTransformerFactory;
use Abacaphiliac\Zend\Transformer\TransformerInterface;
use AbacaphiliacTest\FizBuz;
use AbacaphiliacTest\FooBar;
use Zend\Hydrator\ClassMethods;
use Zend\ServiceManager\ServiceManager;
use Zend\Validator\ValidatorChain;

/**
 * @covers \Abacaphiliac\Zend\Transformer\Factory\AbstractTransformerFactory
 */
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
     * @expectedException \Abacaphiliac\Zend\Transformer\Exception\TransformationException
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
     * @expectedException \Abacaphiliac\Zend\Transformer\Exception\TransformationException
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
    
    public function testTransformCustomExtractor()
    {
        $this->container->setService('config', [
            'abacaphiliac/zend-transformer' => [
                'transformers' => [
                    'FooBarToFizBuz' => [
                        'inputClass' => FooBar::class,
                        'extractor' => ClassMethods::class,
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
    
    public function testTransformCustomExtractorInstance()
    {
        $this->container->setService('config', [
            'abacaphiliac/zend-transformer' => [
                'transformers' => [
                    'FooBarToFizBuz' => [
                        'inputClass' => FooBar::class,
                        'extractor' => new ClassMethods(),
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
    
    public function testTransformCustomHydrator()
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
                        'hydrator' => ClassMethods::class,
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
    
    public function testTransformCustomHydratorInstance()
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
                        'hydrator' => new ClassMethods(),
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
    
    public function testTransformCustomKeyMapCallable()
    {
        $this->container->setService('config', [
            'abacaphiliac/zend-transformer' => [
                'transformers' => [
                    'FooBarToFizBuz' => [
                        'inputClass' => FooBar::class,
                        'transformer' => function (array $data) {
                            return [
                                'fiz' => $data['foo'],
                                'buz' => $data['bar'],
                            ];
                        },
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
    
    public function testTransformCustomKeyMapService()
    {
        $this->container->setService('CustomKeyMapService', function (array $data) {
            return [
                'fiz' => $data['foo'],
                'buz' => $data['bar'],
            ];
        });
        
        $this->container->setService('config', [
            'abacaphiliac/zend-transformer' => [
                'transformers' => [
                    'FooBarToFizBuz' => [
                        'inputClass' => FooBar::class,
                        'transformer' => 'CustomKeyMapService',
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
    
    public function testTransformCustomInputValidatorInstance()
    {
        $this->container->setService('config', [
            'abacaphiliac/zend-transformer' => [
                'transformers' => [
                    'FooBarToFizBuz' => [
                        'inputClass' => FooBar::class,
                        'inputValidator' => new ValidatorChain(),
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
    
    public function testTransformCustomOutputValidatorInstance()
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
                        'outputValidator' => new ValidatorChain(),
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
}

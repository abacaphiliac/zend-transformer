<?php

namespace AbacaphiliacTest\Zend\Transformer;

use Abacaphiliac\Zend\Transformer\Transformer;
use AbacaphiliacTest\FizBuz;
use AbacaphiliacTest\FooBar;
use Zend\Hydrator\ClassMethods;
use Zend\Validator\ValidatorChain;

/**
 * @covers \Abacaphiliac\Zend\Transformer\Transformer
 */
class TransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $sut = new Transformer(
            new ValidatorChain(),
            new ClassMethods(),
            function (array $data) {
                return [
                    'fiz' => $data['foo'],
                    'buz' => $data['bar'],
                ];
            },
            new ClassMethods(),
            new ValidatorChain()
        );
    
        $input = new FooBar();
        $input->setFoo('Alice');
        $input->setBar('Bob');
        
        $output = $sut->transform($input, FizBuz::class);
        self::assertInstanceOf(FizBuz::class, $output);
        
        /** @var FizBuz $output */
        self::assertEquals('Alice', $output->getFiz());
        self::assertEquals('Bob', $output->getBuz());
    }
}

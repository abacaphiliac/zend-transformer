<?php

namespace AbacaphiliacTest\Zend\Transformer;

use Abacaphiliac\Zend\Transformer\Module;

/**
 * @covers \Abacaphiliac\Zend\Transformer\Module
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfig()
    {
        $config = (new Module())->getConfig();
        
        self::assertArraySubset($config, unserialize(serialize($config)));
    }
}

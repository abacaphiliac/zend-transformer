<?php

namespace AbacaphiliacTest\Zend\Transformer\PluginManager;

use Abacaphiliac\Zend\Transformer\Module;
use Abacaphiliac\Zend\Transformer\PluginManager\TransformerPluginManager;
use Abacaphiliac\Zend\Transformer\TransformerInterface;
use AbacaphiliacTest\FizBuz;
use AbacaphiliacTest\FooBar;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers \Abacaphiliac\Zend\Transformer\PluginManager\TransformerPluginManager
 */
class TransformerPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateTransformerViaModuleConfig()
    {
        $module = new Module();
        $config = $module->getConfig();
        
        $config['abacaphiliac/zend-transformer']['transformers']['FooBarToFizBuz'] = [
            'inputClass' => FooBar::class,
            'keyMap' => [
                'foo' => 'fiz',
                'bar' => 'buz',
            ],
            'outputClass' => FizBuz::class,
        ];
        
        $serviceManagerConfig = new ServiceManagerConfig(\igorw\get_in($config, ['service_manager'], []));
        
        $container = new ServiceManager();
        $serviceManagerConfig->configureServiceManager($container);
        $container->setService('config', $config);
    
        $transformers = $container->get('TransformerManager');
        self::assertInstanceOf(TransformerPluginManager::class, $transformers);
    
        $transformer = $transformers->get('FooBarToFizBuz');
        self::assertInstanceOf(TransformerInterface::class, $transformer);
    }
}

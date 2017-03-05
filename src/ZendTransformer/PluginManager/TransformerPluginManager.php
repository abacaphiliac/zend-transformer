<?php

namespace Abacaphiliac\Zend\Transformer\PluginManager;

use Abacaphiliac\Zend\Transformer\Factory\AbstractTransformerFactory;
use Abacaphiliac\Zend\Transformer\TransformerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class TransformerPluginManager extends AbstractPluginManager
{
    /**
     * TransformerPluginManager constructor.
     * @param mixed $configOrContainerInstance
     * @param array $v3config
     * @throws \Zend\ServiceManager\Exception\InvalidArgumentException
     */
    public function __construct($configOrContainerInstance = null, array $v3config = [])
    {
        parent::__construct($configOrContainerInstance, $v3config);
        
        $this->abstractFactories[] = new AbstractTransformerFactory();
    }
    
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof TransformerInterface) {
            throw new Exception\RuntimeException(sprintf(
                'Expected class %s. Actual type %s class %s.',
                TransformerInterface::class,
                gettype($plugin),
                is_object($plugin) ? get_class($plugin) : null
            ));
        }
    }
}

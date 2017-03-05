<?php

namespace Abacaphiliac\Zend\Transformer\PluginManager;

use Abacaphiliac\Zend\Transformer\Factory\AbstractTransformerFactory;
use Abacaphiliac\Zend\Transformer\TransformerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Exception;

class TransformerPluginManager extends AbstractPluginManager
{
    /**
     * TransformerPluginManager constructor.
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        
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

<?php

namespace Abacaphiliac\Zend\Transformer\PluginManager;

use Abacaphiliac\Zend\Transformer\Factory\AbstractTransformerFactory;
use Abacaphiliac\Zend\Transformer\TransformerInterface;
use Zend\ServiceManager\AbstractPluginManager;

class TransformerPluginManager extends AbstractPluginManager
{
    /**
     * TransformerPluginManager constructor.
     * @param null $configInstanceOrParentLocator
     * @param array $config
     */
    public function __construct($configInstanceOrParentLocator = null, array $config = [])
    {
        parent::__construct($configInstanceOrParentLocator, $config);
        
        $this->abstractFactories[] = AbstractTransformerFactory::class;
        $this->instanceOf = TransformerInterface::class;
    }
}

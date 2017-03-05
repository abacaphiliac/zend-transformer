<?php

namespace Abacaphiliac\Zend\Transformer;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return require __DIR__ . '/../../config/module.config.php';
    }
}

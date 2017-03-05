<?php

namespace Abacaphiliac\Zend\Transformer\Factory;

use Abacaphiliac\Zend\Transformer\Config\TransformerConfig;
use Abacaphiliac\Zend\Transformer\Transformer;
use Assert\Assertion;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\ExtractionInterface;
use Zend\Hydrator\HydrationInterface;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\IsInstanceOf;
use Zend\Validator\ValidatorChain;
use Zend\Validator\ValidatorInterface;
use Zend\Validator\ValidatorPluginManager;

class AbstractTransformerFactory implements AbstractFactoryInterface
{
    private static $pluginManagers = [
        'HydratorManager' => HydratorPluginManager::class,
        'ValidatorManager' => ValidatorPluginManager::class,
    ];
    
    /**
     * Can the factory create an instance for the service?
     *
     * @param ServiceLocatorInterface $container
     * @param string $name
     * @param string $requestedName
     * @return bool
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $container, $name, $requestedName)
    {
        if ($container instanceof AbstractPluginManager) {
            $container = $container->getServiceLocator();
        }
        
        return is_array($this->getTransformerConfig($container, $requestedName));
    }
    
    /**
     * Create an object
     *
     * @param ServiceLocatorInterface $container
     * @param string $name
     * @param string $requestedName
     * @return Transformer
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     * @throws \Assert\AssertionFailedException
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @internal param array|null $options
     */
    public function createServiceWithName(ServiceLocatorInterface $container, $name, $requestedName)
    {
        if ($container instanceof AbstractPluginManager) {
            $container = $container->getServiceLocator();
        }
        
        $config = new TransformerConfig($this->getTransformerConfig($container, $requestedName));
    
        $inputValidator = $this->getValidator($container, $config->getInputValidator(), $config->getInputClass());
        $extractor = $this->getExtractor($container, $config->getExtractor());
        $transformer = $this->getTransformer($container, $config);
        $hydrator = $this->getHydrator($container, $config->getHydrator());
        $outputValidator = $this->getValidator($container, $config->getOutputValidator(), $config->getOutputClass());
        
        return new Transformer(
            $inputValidator,
            $extractor,
            $transformer,
            $hydrator,
            $outputValidator
        );
    }
    
    /**
     * @param ServiceLocatorInterface $container
     * @param string $requestedName
     * @return mixed[]
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    private function getTransformerConfig(ServiceLocatorInterface $container, $requestedName)
    {
        $applicationConfig = $container->get('config');
    
        return \igorw\get_in($applicationConfig, ['abacaphiliac/zend-transformer', 'transformers', $requestedName]);
    }
    
    /**
     * @param ServiceLocatorInterface $container
     * @param string $service
     * @param string $validateIsInstanceOf
     * @return ValidatorInterface
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Assert\AssertionFailedException
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     * @internal param TransformerConfig $config
     */
    private function getValidator(ServiceLocatorInterface $container, $service, $validateIsInstanceOf = null)
    {
        if ($service instanceof ValidatorInterface) {
            return $service;
        }
        
        if ($service === null) {
            $validator = new ValidatorChain();
            
            if ($validateIsInstanceOf) {
                $validator->attach(new IsInstanceOf(['className' => $validateIsInstanceOf]));
            }
            
            return $validator;
        }
        
        $validator = $this->getService($container, $service, 'ValidatorManager');
        Assertion::isInstanceOf($validator, ValidatorInterface::class);
        
        /** @var ValidatorInterface $validator */
        return $validator;
    }
    
    /**
     * @param ServiceLocatorInterface $container
     * @param string $service
     * @return ExtractionInterface
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Assert\AssertionFailedException
     */
    private function getExtractor(ServiceLocatorInterface $container, $service)
    {
        if ($service instanceof ExtractionInterface) {
            return $service;
        }
        
        if ($service === null) {
            return new ClassMethods();
        }
        
        $extractor = $this->getService($container, $service, 'HydratorManager');
        Assertion::isInstanceOf($extractor, ExtractionInterface::class);
        
        /** @var ExtractionInterface $extractor */
        return $extractor;
    }
    
    /**
     * @param ServiceLocatorInterface $container
     * @param string $service
     * @return HydrationInterface
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Assert\AssertionFailedException
     */
    private function getHydrator(ServiceLocatorInterface $container, $service)
    {
        if ($service instanceof HydrationInterface) {
            return $service;
        }
        
        if ($service === null) {
            return new ClassMethods();
        }
        
        $validator = $this->getService($container, $service, 'HydratorManager');
        Assertion::isInstanceOf($validator, HydrationInterface::class);
        
        /** @var HydrationInterface $validator */
        return $validator;
    }
    
    /**
     * @param ServiceLocatorInterface $container
     * @param string $pluginManagerName
     * @param string $service
     * @return Object
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     * @throws \Assert\AssertionFailedException
     */
    private function getService(ServiceLocatorInterface $container, $service, $pluginManagerName)
    {
        $plugins = null;
        
        if ($container->has($pluginManagerName)) {
            // Get the named plugin-manager from parent container.
            $plugins = $container->get($pluginManagerName);
            Assertion::isInstanceOf($plugins, AbstractPluginManager::class);
        } else if (isset(self::$pluginManagers[$pluginManagerName])) {
            // Create a new plugin-manager.
            $plugins = new self::$pluginManagers[$pluginManagerName];
            Assertion::isInstanceOf($plugins, AbstractPluginManager::class);
            
            /** @var AbstractPluginManager $plugins */
            $plugins->setServiceLocator($container);
        }
        
        if ($plugins instanceof ServiceLocatorInterface && $plugins->has($service)) {
            // Get the service/plugin from the plugin-manager.
            return $plugins->get($service);
        }
    
        // Fall-back to parent container for service since it could not be provided by a plugin-manager.
        return $container->get($service);
    }
    
    /**
     * @param ServiceLocatorInterface $container
     * @param TransformerConfig $config
     * @return callable
     * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    private function getTransformer(ServiceLocatorInterface $container, TransformerConfig $config)
    {
        $transformer = $config->getTransformer();
        if (is_callable($transformer)) {
            return $transformer;
        }
    
        $keyMap = $config->getKeyMap();
        if (is_array($keyMap)) {
            return function (array $data) use ($keyMap) {
                $result = [];
                foreach ($data as $key => $value) {
                    $result[\igorw\get_in($keyMap, [$key], $key)] = $value;
                }
                
                return $result;
            };
        }
        
        $transformer = $container->get($transformer);
        Assertion::isCallable($transformer);
        
        return $transformer;
    }
}

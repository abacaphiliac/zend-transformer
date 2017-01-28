<?php

namespace Abacaphiliac\Zend\Transformer\Factory;

use Abacaphiliac\Zend\Transformer\Config\TransformerConfig;
use Abacaphiliac\Zend\Transformer\Transformer;
use Assert\Assertion;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\ExtractionInterface;
use Zend\Hydrator\HydrationInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Zend\Validator\IsInstanceOf;
use Zend\Validator\ValidatorChain;
use Zend\Validator\ValidatorInterface;

class AbstractTransformerFactory implements AbstractFactoryInterface
{
    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @return bool
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return is_array($this->getTransformerConfig($container, $requestedName));
    }
    
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return Transformer
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Assert\AssertionFailedException
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
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
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return mixed[]
     * @throws \Interop\Container\Exception\ContainerException
     */
    private function getTransformerConfig(ContainerInterface $container, $requestedName)
    {
        $applicationConfig = $container->get('config');
    
        return \igorw\get_in($applicationConfig, ['abacaphiliac/zend-transformer', 'transformers', $requestedName]);
    }
    
    /**
     * @param ContainerInterface $container
     * @param string $service
     * @param string $validateIsInstanceOf
     * @return ValidatorInterface
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Assert\AssertionFailedException
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     * @internal param TransformerConfig $config
     */
    private function getValidator(ContainerInterface $container, $service, $validateIsInstanceOf = null)
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
     * @param ContainerInterface $container
     * @param string $service
     * @return ExtractionInterface
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Assert\AssertionFailedException
     */
    private function getExtractor(ContainerInterface $container, $service)
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
     * @param ContainerInterface $container
     * @param string $service
     * @return HydrationInterface
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Assert\AssertionFailedException
     */
    private function getHydrator(ContainerInterface $container, $service)
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
     * @param ContainerInterface $container
     * @param string $pluginManagerName
     * @param string $service
     * @return Object
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Assert\AssertionFailedException
     */
    private function getService(ContainerInterface $container, $service, $pluginManagerName)
    {
        if ($container->has($pluginManagerName)) {
            $validators = $container->get($pluginManagerName);
            Assertion::isInstanceOf($validators, ContainerInterface::class);
            
            /** @var ContainerInterface $validators */
            if ($validators->has($service)) {
                return $validators->get($service);
            }
        }
    
        return $container->get($service);
    }
    
    /**
     * @param ContainerInterface $container
     * @param TransformerConfig $config
     * @return callable
     * @throws \Interop\Container\Exception\ContainerException
     */
    private function getTransformer(ContainerInterface $container, TransformerConfig $config)
    {
        $transformer = $config->getTransformer();
        if (is_callable($transformer)) {
            return $transformer;
        }
    
        $transformer = $config->getKeyMap();
        if (is_array($transformer)) {
            return function (array $data) use ($transformer) {
                $result = [];
                foreach ($data as $key => $value) {
                    $result[\igorw\get_in($transformer, [$key], $key)] = $value;
                }
                
                return $result;
            };
        }
        
        $transformer = $container->get($transformer);
        Assertion::isCallable($transformer);
        
        return $transformer;
    }
}
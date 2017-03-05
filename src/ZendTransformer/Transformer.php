<?php

namespace Abacaphiliac\Zend\Transformer;

use Abacaphiliac\Zend\Transformer\Exception\TransformationException;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Zend\Hydrator\ExtractionInterface;
use Zend\Hydrator\HydrationInterface;
use Zend\Validator\ValidatorInterface;

class Transformer implements TransformerInterface
{
    /** @var ValidatorInterface */
    private $inputValidator;
    
    /** @var ExtractionInterface */
    private $extractor;
    
    /** @var callable */
    private $transformer;
    
    /** @var HydrationInterface */
    private $hydrator;
    
    /** @var ValidatorInterface */
    private $outputValidator;
    
    /**
     * Transformer constructor.
     * @param ValidatorInterface $inputValidator
     * @param ExtractionInterface $extractor
     * @param callable $transformer
     * @param HydrationInterface $hydrator
     * @param ValidatorInterface $outputValidator
     */
    public function __construct(
        ValidatorInterface $inputValidator,
        ExtractionInterface $extractor,
        callable $transformer,
        HydrationInterface $hydrator,
        ValidatorInterface $outputValidator
    ) {
        $this->inputValidator = $inputValidator;
        $this->extractor = $extractor;
        $this->transformer = $transformer;
        $this->hydrator = $hydrator;
        $this->outputValidator = $outputValidator;
    }
    
    /**
     * @param mixed $input
     * @param mixed|string $output
     * @return mixed
     * @throws \Abacaphiliac\Zend\Transformer\Exception\TransformationException
     */
    public function transform($input, $output)
    {
        try {
            if (is_string($output)) {
                Assertion::classExists($output);
                $output = new $output;
            }
            
            $this->validateObject($input, $this->inputValidator);
        
            $inputData = $this->extract($input);
        
            $outputData = $this->transformInputData($inputData);
        
            $output = $this->hydrate($outputData, $output);
        
            $this->validateObject($output, $this->outputValidator);
        } catch (AssertionFailedException $e) {
            throw new TransformationException('Transformation failed.', null, $e);
        } catch (\Exception $e) {
            throw new TransformationException('Transformation failed.', null, $e);
        }
        
        return $output;
    }
    
    /**
     * @param mixed $object
     * @param ValidatorInterface $validator
     * @return void
     * @throws \Zend\Validator\Exception\RuntimeException
     * @throws AssertionFailedException
     */
    private function validateObject($object, ValidatorInterface $validator)
    {
        Assertion::isObject($object);
    
        $isValid = $validator->isValid($object);
    
        Assertion::true($isValid, 'Validation failed: ' . json_encode($validator->getMessages()));
    }
    
    /**
     * @param mixed $object
     * @return mixed[]
     * @throws AssertionFailedException
     */
    private function extract($object)
    {
        Assertion::isObject($object);
        
        $data = $this->extractor->extract($object);
        
        Assertion::isArray($data);
        
        return $data;
    }
    
    /**
     * @param mixed[] $input
     * @return mixed[]
     * @throws AssertionFailedException
     */
    private function transformInputData(array $input)
    {
        $output = call_user_func($this->transformer, $input);
        
        Assertion::isArray($output);
        
        return $output;
    }
    
    /**
     * @param mixed[] $data
     * @param mixed|string $object
     * @return mixed
     * @throws AssertionFailedException
     */
    private function hydrate(array $data, $object)
    {
        Assertion::isObject($object);
        
        $output = $this->hydrator->hydrate($data, $object);
        
        Assertion::isObject($output);
        
        return $output;
    }
}

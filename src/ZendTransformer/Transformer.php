<?php

namespace Abacaphiliac\Zend\Transformer;

use Abacaphiliac\Zend\Transformer\Exception\ExtractionException;
use Abacaphiliac\Zend\Transformer\Exception\HydrationException;
use Abacaphiliac\Zend\Transformer\Exception\TransformationException;
use Abacaphiliac\Zend\Transformer\Exception\ValidationException;
use Zend\Hydrator\ExtractionInterface;
use Zend\Hydrator\HydrationInterface;
use Zend\Validator\ValidatorInterface;

// TODO Abstract? Spec-Based? Config-Based?
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
     * @param Object $input
     * @param Object $output
     * @return Object
     * @throws \Abacaphiliac\Zend\Transformer\Exception\ExceptionInterface
     */
    public function transform($input, $output)
    {
        $this->validateOutputParameter($output);
        
        $this->validateObject($input, $this->inputValidator);
    
        $inputData = $this->extract($input);
    
        $outputData = $this->transformInputData($inputData);
    
        $output = $this->hydrate($outputData, $output);
    
        $this->validateObject($output, $this->outputValidator);
        
        return $output;
    }
    
    /**
     * @param Object $output
     * @return bool
     * @throws \Abacaphiliac\Zend\Transformer\Exception\ValidationException
     */
    private function validateOutputParameter($output)
    {
        if (is_object($output)) {
            return true;
        }
        
        if (!is_string($output)) {
            throw new ValidationException('Output must be an object or a class name.', 0); // TODO Describe input.
        }
    
        if (!class_exists($output)) {
            throw new ValidationException('Output must be an object or a class name.', 0); // TODO Describe input.
        }
        
        return true;
    }
    
    /**
     * @param Object $object
     * @param ValidatorInterface $validator
     * @return bool
     * @throws \Abacaphiliac\Zend\Transformer\Exception\ValidationException
     */
    private function validateObject($object, ValidatorInterface $validator)
    {
        if (!is_object($object)) {
            throw new ValidationException('Transformation input failed validation.', 0); // TODO Describe input.
        }
    
        try {
            $isInputValid = $validator->isValid($object);
        } catch (\Exception $e) {
            throw new ValidationException('Validation failed due to an exception.', 0, $e); // TODO Describe input.
        }
    
        if (!$isInputValid) {
            throw new ValidationException('Transformation input failed validation.', 0); // TODO Describe input.
        }
        
        return true;
    }
    
    /**
     * @param Object $object
     * @return mixed[]
     * @throws \Abacaphiliac\Zend\Transformer\Exception\ExtractionException
     */
    private function extract($object)
    {
        try {
            $data = $this->extractor->extract($object);
        } catch (\Exception $e) {
            throw new ExtractionException('Extraction of input data failed.', 0, $e);
        }
        
        return $data;
    }
    
    /**
     * @param mixed[] $input
     * @return mixed[]
     * @throws \Abacaphiliac\Zend\Transformer\Exception\TransformationException
     */
    private function transformInputData($input)
    {
        try {
            $output = call_user_func($this->transformer, $input);
        } catch (\Exception $e) {
            throw new TransformationException('Transformation of input data failed.', 0, $e);
        }
        
        if (!is_array($output)) {
            throw new TransformationException('Transformation result must be an array.', 0); // TODO Describe.
        }
        
        return $output;
    }
    
    /**
     * @param mixed[] $data
     * @param Object $object
     * @return Object
     * @throws \Abacaphiliac\Zend\Transformer\Exception\HydrationException
     */
    private function hydrate(array $data, $object)
    {
        try {
            $object = $this->hydrator->hydrate($data, $object);
        } catch (\Exception $e) {
            throw new HydrationException('Extraction of input data failed.', 0, $e);
        }
        
        return $object;
    }
}

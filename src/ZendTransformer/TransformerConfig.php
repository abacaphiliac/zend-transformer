<?php

namespace Abacaphiliac\Zend\Transformer;

use Zend\Stdlib\AbstractOptions;

class TransformerConfig extends AbstractOptions
{
    /** @var string|null */
    private $inputValidator;
    
    /** @var string|null */
    private $extractor;
    
    /** @var string|mixed[]|null */
    private $transformer;
    
    /** @var string|null */
    private $hydrator;
    
    /** @var string|null */
    private $outputValidator;
    
    /**
     * @return null|string
     */
    public function getInputValidator()
    {
        return $this->inputValidator;
    }
    
    /**
     * @param null|string $inputValidator
     * @return void
     */
    public function setInputValidator($inputValidator)
    {
        $this->inputValidator = $inputValidator;
    }
    
    /**
     * @return null|string
     */
    public function getExtractor()
    {
        return $this->extractor;
    }
    
    /**
     * @param null|string $extractor
     * @return void
     */
    public function setExtractor($extractor)
    {
        $this->extractor = $extractor;
    }
    
    /**
     * @return \mixed[]|null|string
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
    
    /**
     * @param \mixed[]|null|string $transformer
     * @return void
     */
    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;
    }
    
    /**
     * @return null|string
     */
    public function getHydrator()
    {
        return $this->hydrator;
    }
    
    /**
     * @param null|string $hydrator
     * @return void
     */
    public function setHydrator($hydrator)
    {
        $this->hydrator = $hydrator;
    }
    
    /**
     * @return null|string
     */
    public function getOutputValidator()
    {
        return $this->outputValidator;
    }
    
    /**
     * @param null|string $outputValidator
     * @return void
     */
    public function setOutputValidator($outputValidator)
    {
        $this->outputValidator = $outputValidator;
    }
}

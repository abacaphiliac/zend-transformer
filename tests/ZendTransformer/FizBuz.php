<?php

namespace AbacaphiliacTest;

class FizBuz
{
    /** @var mixed|null */
    private $fiz;
    
    /** @var mixed|null */
    private $buz;
    
    /**
     * FizBuz constructor.
     * @param mixed|null $fiz
     * @param mixed|null $buz
     */
    public function __construct($fiz = null, $buz = null)
    {
        $this->fiz = $fiz;
        $this->buz = $buz;
    }
    
    /**
     * @return mixed|null
     */
    public function getFiz()
    {
        return $this->fiz;
    }
    
    /**
     * @param mixed|null $fiz
     * @return void
     */
    public function setFiz($fiz)
    {
        $this->fiz = $fiz;
    }
    
    /**
     * @return mixed|null
     */
    public function getBuz()
    {
        return $this->buz;
    }
    
    /**
     * @param mixed|null $buz
     * @return void
     */
    public function setBuz($buz)
    {
        $this->buz = $buz;
    }
}

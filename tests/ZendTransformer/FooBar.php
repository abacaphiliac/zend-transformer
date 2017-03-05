<?php

namespace AbacaphiliacTest;

class FooBar
{
    /** @var mixed|null */
    private $foo;
    
    /** @var mixed|null */
    private $bar;
    
    /**
     * FooBar constructor.
     * @param mixed|null $foo
     * @param mixed|null $bar
     */
    public function __construct($foo = null, $bar = null)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
    
    /**
     * @return mixed|null
     */
    public function getFoo()
    {
        return $this->foo;
    }
    
    /**
     * @param mixed|null $foo
     * @return void
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
    
    /**
     * @return mixed|null
     */
    public function getBar()
    {
        return $this->bar;
    }
    
    /**
     * @param mixed|null $bar
     * @return void
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
    }
}

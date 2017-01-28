<?php

namespace Abacaphiliac\Zend\Transformer;


interface TransformerInterface
{
    /**
     * @param Object $input
     * @param Object $output Class name or model.
     * @return Object
     * @throws \Abacaphiliac\Zend\Transformer\Exception\ExceptionInterface
     */
    public function transform($input, $output);
}

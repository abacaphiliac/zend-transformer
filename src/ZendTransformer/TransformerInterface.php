<?php

namespace Abacaphiliac\Zend\Transformer;

interface TransformerInterface
{
    /**
     * @param object $input
     * @param object $output Class name or model.
     * @return object
     * @throws \Abacaphiliac\Zend\Transformer\Exception\ExceptionInterface
     */
    public function transform($input, $output);
}

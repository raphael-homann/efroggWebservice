<?php

namespace Efrogg\Webservice\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpJsonException extends HttpException {
    protected $json;

    public function __construct($statusCode, $json = null, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $this->json = $json;
        parent::__construct($statusCode, '',  $previous ,  $headers , $code);
    }

    /**
     * @return null
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param null $json
     */
    public function setJson($json)
    {
        $this->json = $json;
    }

}
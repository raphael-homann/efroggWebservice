<?php
namespace efrogg\Webservice\Exception;

class ValidationException extends HttpJsonException
{
    public function __construct($statusCode, $errors = null, \Exception $previous = null, array $headers = array(), $code = 0)
    {
        $formatedErrors = array();
        foreach ($errors as $error) {
            $formatedErrors[$error->getPropertyPath()] = $error->getMessage();
        }

        parent::__construct($statusCode, $formatedErrors,  $previous ,  $headers , $code);
    }
}
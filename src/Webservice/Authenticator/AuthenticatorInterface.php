<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 02/09/16
 * Time: 08:47
 */

namespace Efrogg\Webservice\Authenticator;


use Symfony\Component\HttpFoundation\Response;

interface AuthenticatorInterface
{
    /**
     * @return bool
     */
    public function tryAuth();

    /**
     * @return Response
     */
    public function getResponse();
}
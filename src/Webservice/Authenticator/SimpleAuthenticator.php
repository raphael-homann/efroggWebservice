<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 31/05/16
 * Time: 10:57
 */

namespace efrogg\Webservice\Authenticator;


use Symfony\Component\HttpFoundation\Response;

class SimpleAuthenticator implements AuthenticatorInterface
{
    protected $key = "-------------------------------";

    /** @var  Response */
    protected $response;

    /**
     * WebserviceAuthenticator constructor.
     */
    public function __construct($key)
    {
        $this->key=$key;
    }

    /**
     * @return bool
     */
    public function tryAuth()
    {
        // controle auth
        //set http auth headers for apache+php-cgi work around
        if (isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'],
                $matches)
        ) {
            list($name, $password) = explode(':', base64_decode($matches[1]));
            $_SERVER['PHP_AUTH_USER'] = strip_tags($name);
        }

        //set http auth headers for apache+php-cgi work around if variable gets renamed by apache
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && preg_match('/Basic\s+(.*)$/i',
                $_SERVER['REDIRECT_HTTP_AUTHORIZATION'], $matches)
        ) {
            list($name, $password) = explode(':', base64_decode($matches[1]));
            $_SERVER['PHP_AUTH_USER'] = strip_tags($name);
        }

        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $key = $_SERVER['PHP_AUTH_USER'];
        } elseif (isset($_GET['ws_key'])) {
            $key = $_GET['ws_key'];
        } else {
            $this->response = new Response("Unauthorized",401,array(
                "WWW-Authenticate" => 'Basic realm="Welcome to PrestaShop Webservice, please enter the authentication key as the login. No password required."'
            ));
            return false;
        }

        if ($key != $this->key) {
            $this->response = new Response("Unauthorized",401,array(
                "WWW-Authenticate" => 'Basic realm="Welcome to PrestaShop Webservice, please enter the authentication key as the login. No password required."'
            ));
            return false;
        }

        return true;

    }

    /**
     * @param string $key
     * @return SimpleAuthenticator
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

}
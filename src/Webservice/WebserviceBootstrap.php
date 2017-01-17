<?php

namespace Efrogg\Webservice;

use Efrogg\Db\Adapters\DbAdapter;
use Efrogg\Webservice\Authenticator\AuthenticatorInterface;
use Efrogg\Webservice\Authenticator\SimpleAuthenticator;
use Efrogg\Webservice\Exception\HttpJsonException;
use ErpConnector\Response\ApiResponse;
use Exception;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WebserviceBootstrap {

    protected $app;

    protected $allowDebug = false;

    public function __construct($options=array())
    {
        $this->allowDebug = (isset($options["allow_debug"]) && $options["allow_debug"]);

        $this->app = new Application();
        if ($this->allowDebug ) {   //TODO && isset($_SERVER['HTTP_MODEDEV']) && $_SERVER['HTTP_MODEDEV'] == 1
            $this->app['debug'] = true;
            ini_set("display_errors", "on");
            error_reporting(E_ALL);
        }

        $this->app->register(new ServiceControllerServiceProvider());
        $this->app->register(new ValidatorServiceProvider());

        if (!$this->app['debug']) {
            // catch les exceptions
            $app = $this->app;
            $this->app->error(function (Exception $e) use ($app) {
                $response = ApiResponse::create();

                if ($e instanceof HttpJsonException) {
                    // erreur HTTP, on passe son statut_code
                    $response->setStatusCode($e->getStatusCode());
                    $jsonRespondeData = array(
                        "error" => array(
                            "code" => $e->getCode(),
                            "message" => $e->getMessage(),
                            "detail" => $e->getJson()
                        )
                    );
                } elseif ($e instanceof HttpException) {
                    // erreur HTTP, on passe son statut_code
                    $response->setStatusCode($e->getStatusCode());
                    $jsonRespondeData = array(
                        "error" => array(
                            "code" => $e->getCode(),
                            "message" => $e->getMessage(),
                        )
                    );
                } else {
                    // toute erreur
                    $response->setStatusCode(500);
                    $jsonRespondeData = array(
                        "error" => array(
                            "code" => $e->getCode(),
                            "message" => (!$this->app['debug']) ? "Erreur serveur" : $e->getMessage(),
                        )
                    );
                }

                $response->setData($jsonRespondeData);
                return $response;
            });
        }

        $this->app->after(function (Request $request, Response $response, Application $application) {
            if ($request->attributes->has('request_time')) {
                $response->headers->add(array(
                    'Api-Request-Timestamp' => $request->attributes->get('request_time'),
                    'Api-Response-Timestamp' => date_timestamp_get(date_create())
                ));
            }
        });
        // dépendances

    }

    public function after(callable $callback) {
        $this->app->after($callback);
    }

    public function setAuthenticator(AuthenticatorInterface $authenticator) {
        $this->app->before(function (Request $request, Application $app) use($authenticator) {
            if ($authenticator->tryAuth()) {
                if (!empty($request->getContent())) {
                    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                        $data = json_decode($request->getContent(), true);
                        // validation JSON
                        if (is_null($data)) {
                            // json mal formé
                            throw new HttpException(400, "invalid JSON");
                        }
                        $request->request->replace(is_array($data) ? $data : array());
                    } else {
                        // réponse non json
                        throw new HttpException(400, "non JSON request");
                    }
                }
            } else {
                return $authenticator->getResponse();
            }

            $request->attributes->add(array(
                'request_time' => date_timestamp_get(date_create())
            ));
        }, Application::EARLY_EVENT);

    }

    /**
     * définit une authentification simple (user)
     * @param $user
     * @param string $pass
     */
    public function setAuth($user,$pass='') {
        $this -> setAuthenticator(new SimpleAuthenticator($user));
    }

    public function run() {
        $this->app->run();
    }

    public function setDb(DbAdapter $dbAdapter)
    {
        $this->app['db'] = $dbAdapter;
    }

    public function addProvider($path,ControllerProviderInterface $provider)
    {
        $this->app->mount($path, $provider);
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

}
<?php

namespace efrogg\Webservice;

use efrogg\Db\Adapters\DbAdapter;
use efrogg\Webservice\Exception\HttpJsonException;
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

    public function __construct()
    {

        $this->app = new Application();
        if (EST_EFROGG && isset($_SERVER['HTTP_MODEDEV']) && $_SERVER['HTTP_MODEDEV'] == 1) {   //TODO
            $app['debug'] = true;
            ini_set("display_errors", "on");
            error_reporting(E_ALL);
        }

        $this->app->register(new ServiceControllerServiceProvider());
        $this->app->register(new ValidatorServiceProvider());

// auth
        $this->app->before(function (Request $request, Application $app) {
            $authenticator = new WebserviceAuthenticator("9D22NNDJ721JHMMGECKRPTMHHKPGHPPV");       //TODO
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

        if (!$this->app['debug']) {
            $app = $this->app;
            $this->app->error(function (Exception $e) {
                $response = new JsonResponse();

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
}
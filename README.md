# htaccess
Créer un fichier .htaccess : 

    RewriteBase /
    RewriteRule ^api/(.*)$ webservice.php [QSA,L]
    
# index
Créer un fichier webservice.php :

    <?php
    
    $autoloader = require "vendor/autoload.php";
    
    $options = array("allow_debug"=>true);
    
    $bootstrap = new WebserviceBootstrap($options);
    $bootstrap->setAuthenticator(new SimpleAuthenticator("XXX"));
    $bootstrap->addProvider("/api",new WebserviceCmsProvider($bootstrap->getApp()));
    //$bootstrap->setDb(new PrestashopDbAdapter(\Db::getInstance()));
    
    $bootstrap->run();
    
#Provider
on ajoute un ou plusieurs Providers,qui dispatch sous un dossier (ici /api) un certain nombre de services
 
    <?php
    namespace efrogg\Simplecms\Webservice\Provider;
    
    use efrogg\Simplecms\Webservice\WebservicePage;
    use Silex\Api\ControllerProviderInterface;
    use Silex\Application;
    use Silex\ControllerCollection;
    
    class WebserviceCmsProvider implements ControllerProviderInterface
    {
    
        /**
         * Returns routes to connect to the given application.
         *
         * @param Application $app An Application instance
         *
         * @return ControllerCollection A ControllerCollection instance
         */
        public function connect(Application $app)
        {
            /** @var ControllerCollection $controllers */
            $controllers = $app['controllers_factory'];
    
            // order (erp)
            $app["webservice.cms.page"] = function($app) {
                return new WebservicePage($app['db']);
            };
            $controllers->get("/cms/page", "webservice.cms.page:test");
    
    
            return $controllers;
    
        }
    }
    
    
# services
on crée des services

    <?php
    namespace efrogg\Simplecms\Webservice;
    
    use efrogg\Db\Adapters\DbAdapter;
    use Efrogg\Webservice\WebserviceInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Response;
    
    class WebservicePage implements WebserviceInterface
    {
    
        protected $db;
    
        /**
         * @return Response
         */
        public function setDb(DbAdapter $db)
        {
            $this->db = $db;
        }
    
        public function test() {
            return new JsonResponse(array("123","456"=>"789"));
        }
    }
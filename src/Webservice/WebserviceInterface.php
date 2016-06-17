<?php
/**
 * Created by PhpStorm.
 * User: raph
 * Date: 10/05/16
 * Time: 11:05
 */

namespace efrogg\Webservice;

use efrogg\Adapters\Db\DbAdapter;
use Symfony\Component\HttpFoundation\Response;

interface WebserviceInterface {
    /**
     * @param Response $response
     */
//    public function setResponse($response);

    /**
     * @return Response
     */
//    public function getResponse();

//    public function run();

//    public function setMethodName($method);

//    public function setMethodParams($urlSegments);

//    public function initResponse();

    public function setDb(DbAdapter $db);

}
<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2017, Alpha-Hydro
 *
 */

namespace Api\Controller;


use Api\Model\GreetingsTable;
use OAuth2\Server as OAuth2Server;
use OAuth2\Request as OAuth2Request;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class ApiController extends AbstractRestfulController
{

    private $table;
    private $oauthServer;

    public function __construct(GreetingsTable $table, OAuth2Server $server)
    {
        $this->table = $table;
        $this->oauthServer = $server;
    }

    public function indexAction()
    {

        if (!$this->oauthServer->verifyResourceRequest(OAuth2Request::createFromGlobals())) {
            $this->getResponse()->setStatusCode(401);
            return;

        }

        return new JsonModel((array)['id']);
	/*
        $id = (int) $this->params()->fromRoute('id', 0);
        $greeting = $this->table->getGreeting($id);

        return new JsonModel((array)$greeting);
	*/
    }
}

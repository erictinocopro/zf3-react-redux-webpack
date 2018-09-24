<?php
/**
 * Created by Alpha-Hydro.
 * @link http://www.alpha-hydro.com
 * @author Vladimir Mikhaylov <admin@alpha-hydro.com>
 * @copyright Copyright (c) 2017, Alpha-Hydro
 *
 */

namespace Api;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => array(
                Model\GreetingsTable::class => function($container) {
                    $tableGateway = $container->get(Model\GreetingsTableGateway::class);
                    return new Model\GreetingsTable($tableGateway);
                },
                Model\GreetingsTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Greetings());
                    $result = new TableGateway('greetings', $dbAdapter, null, $resultSetPrototype);
                    return $result;
                }
            ),
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\ApiController::class => function($container) {
                    $config = $container->get('Config');
                    $db = $config['zf-oauth2']['db'] ?? null;
                    if (empty($db)) {
                        throw new RuntimeException('missing configuration');

                    }

                    $storage = new \OAuth2\Storage\Pdo([
                        'dsn' => $db['dsn'],
                        'username' => $db['username'],
                        'password' => $db['password']
                    ]);
                    $server = new \OAuth2\Server($storage);
                    $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
                    $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
                    $server->addGrantType(new \OAuth2\GrantType\UserCredentials($storage));

                    return new Controller\ApiController(
                        $container->get(Model\GreetingsTable::class),
                        $server
                    );
                }
            ],
        ];
    }
}

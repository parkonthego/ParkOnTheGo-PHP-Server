<?php

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

return array(
    'factories' => array(
        'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        'Api\Logger' => function () {
            $log = new Zend\Log\Logger();
            $writer = new Zend\Log\Writer\Stream('data/logs/api');
            $log->addWriter($writer);
            return $log;
        },
        /* Controllers */
       
                
         /* DB Models */
        'Api\Model\UserTable' => function ($sm) {
            $tableGateway = $sm->get('UserTableGateway');
            $config = $sm->get("config");
            $logger = $sm->get('Api\Logger');
            $table = new \Api\Model\UserTable($tableGateway, array(
                "config" => $config,
                "logger" => $logger
            ));
            return $table;
        },
        'UserTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \Api\Model\User());
            return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
        },
//        'Api\Model\UserDetailTable' => function ($sm) {
//            $tableGateway = $sm->get('UserDetailTableGateway');
//            $config = $sm->get("config");
//            $logger = $sm->get('Api\Logger');
//            $table = new \Api\Model\UserDetailTable($tableGateway, array(
//                "config" => $config,
//                "logger" => $logger
//            ));
//            return $table;
//        },
//        'UserDetailGateway' => function ($sm) {
//            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
//            $resultSetPrototype = new ResultSet();
//            $resultSetPrototype->setArrayObjectPrototype(new \Api\Model\UserDetail());
//            return new TableGateway('user_detail', $dbAdapter, null, $resultSetPrototype);
//        },
    ),
);


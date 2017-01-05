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
        'Api\Model\ReservationTable' => function ($sm) {
            $tableGateway = $sm->get('ReservationTableGateway');
            $config = $sm->get("config");
            $logger = $sm->get('Api\Logger');
            $table = new \Api\Model\ReservationTable($tableGateway, array(
                "config" => $config,
                "logger" => $logger
            ));
            return $table;
        },
        'ReservationTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \Api\Model\Reservation());
            return new TableGateway('reservation', $dbAdapter, null, $resultSetPrototype);
        },
        'Api\Model\ParkingSlotTable' => function ($sm) {
            $tableGateway = $sm->get('ParkingSlotTableGateway');
            $config = $sm->get("config");
            $logger = $sm->get('Api\Logger');
            $table = new \Api\Model\ParkingSlotTable($tableGateway, array(
                "config" => $config,
                "logger" => $logger
            ));
            return $table;
        },
        'ParkingSlotTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \Api\Model\ParkingSlot());
            return new TableGateway('parking_slot', $dbAdapter, null, $resultSetPrototype);
        },
        'Api\Model\PaymentHistoryTable' => function ($sm) {
            $tableGateway = $sm->get('PaymentHistoryTableGateway');
            $config = $sm->get("config");
            $logger = $sm->get('Api\Logger');
            $table = new \Api\Model\PaymentHistoryTable($tableGateway, array(
                "config" => $config,
                "logger" => $logger
            ));
            return $table;
        },
        'PaymentHistoryTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new \Api\Model\PaymentHistory());
            return new TableGateway('payment_history', $dbAdapter, null, $resultSetPrototype);
        },
    ),
);


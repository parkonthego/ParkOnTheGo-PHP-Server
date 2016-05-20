<?php

namespace Api\ControllerFactory;

use \Zend\ServiceManager\FactoryInterface;
use \Zend\ServiceManager\ServiceLocatorInterface;

class SwaggerControllerFact implements FactoryInterface {

//    public function __invoke(ContainerInterface $container, $name, array $options = null) {
//        $parentLocator = $container->getServiceLocator();
//
//        return new \Api\Controller\SwaggerController($parentLocator->get('config'), $parentLocator->get('viewhelpermanager'));
//    }

    public function createService(ServiceLocatorInterface $container) {
        $parentLocator = $container->getServiceLocator();
      //  return new \Api\Controller\SwaggerController($parentLocator->get('config'), $parentLocator->get('viewhelpermanager'));
         return new \Api\Controller\SwaggerController($parentLocator);
    }

}

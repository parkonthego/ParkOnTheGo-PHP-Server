<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * */

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Http\PhpEnvironment\Request;

class BaseRestfulJsonController extends AbstractRestfulController {

    public $tables = array();
    public $logger;
    public $serviceLocator;
    
    function __construct(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
         $this->serviceLocator = $serviceLocator;
     }
    

    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        $sm = $this->serviceLocator;
        $this->logger = $sm->get("Api/Logger");
        
        return parent::onDispatch($e);
    }

    public function getTable($table) {
        if (!isset($this->tables[$table])) {
            $sm =  $this->serviceLocator;
            $this->tables[$table] = $sm->get('Api\Model\\' . $table . 'Table');
        }
        return $this->tables[$table];
    }

    protected function methodNotAllowed() {
        $this->response->setStatusCode(405);
        throw new \Exception('Method Not Allowed');
    }

    public function success($data = array()) {
        return new JsonModel(array(
            "success" => true,
            "data" => $data
        ));
    }

    public function error($msg, $error_code = 0) {

        return new JsonModel(array(
            "success" => false,
            "error_msg" => $msg,
            "error_code" => $error_code
        ));
    }

    public function authenticationError() {
        return new JsonModel(array(
            "success" => false,
            "error_msg" => "Authentication required, session token is missing or invalid!",
            "error_code" => 401
        ));
    }

    public function authorizationError() {
        return new JsonModel(array(
            "success" => false,
            "error_msg" => "You dont have right permissions to do this operation!",
            "error_code" => 403
        ));
    }
    protected function getBaseUrl() {

        $basePath = $this->getRequest()->getBasePath();
        $uri = new \Zend\Uri\Uri($this->getRequest()->getUri());
        $uri->setPath($basePath);
        $uri->setQuery(array());
        $uri->setFragment('');

        return $uri->getScheme() . '://' . $uri->getHost() . '' . $uri->getPath();
    }
    
    # Override default actions as they do not return valid JsonModels

    public function create($data)
    {
        return $this->methodNotAllowed();
    }

    public function delete($id)
    {
        return $this->methodNotAllowed();
    }

    public function deleteList($data)
    {
        return $this->methodNotAllowed();
    }

    public function get($id)
    {
        return $this->methodNotAllowed();
    }

    public function getList()
    {
        return $this->methodNotAllowed();
    }

    public function head($id = null)
    {
        return $this->methodNotAllowed();
    }

    public function options()
    {
        return $this->methodNotAllowed();
    }

    public function patch($id, $data)
    {
        return $this->methodNotAllowed();
    }

    public function replaceList($data)
    {
        return $this->methodNotAllowed();
    }

    public function patchList($data)
    {
        return $this->methodNotAllowed();
    }

    public function update($id, $data)
    {
        return $this->methodNotAllowed();
    }
    

}

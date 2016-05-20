<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Log\Logger;
use Zend\View\Model\JsonModel;
use Api\Custom\Util;


class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $sm = $e->getApplication()->getServiceManager();
        $log = $sm->get('Api/Logger');

        $config = $sm->get('Config');

        if (isset($config['phpSettings'])) {
            foreach ($config['phpSettings'] as $key => $value) {
                ini_set($key, $value);
            }
        }


        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        //log errors
        Logger::registerFatalErrorShutdownFunction($log);
        Logger::registerExceptionHandler($log);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 0);
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, array($this, 'onRenderError'), 0);

        //show proper message to user
        register_shutdown_function(function () use ($e) {
            $error = error_get_last();
            if (null !== $error && $error['type'] === E_ERROR) {
                exit();
            }
        });

        $controller = $e->getTarget();

        Util::$baseUrl = $this->getBaseUrl($controller);
    }

    public function onDispatchError($e)
    {
        return $this->getJsonModelError($e);
    }

    public function onRenderError($e)
    {
        return $this->getJsonModelError($e);
    }

    public function getJsonModelError($e)
    {
        $error = $e->getError();
        if (!$error) {
            return;
        }

        $response = $e->getResponse();
        $exception = $e->getParam('exception');
        $errorJson = array();
        if ($exception) {
            if (get_class($exception) == 'Api\\Exception\\ApiException') {
                $this->logErrors($e);
                $errorJson = array(
                    'error' => $error,
                    'error_code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                );
                $response->setStatusCode($exception->getCode());
            } else {
                $this->logErrors($e);
                $errorJson = array(
                    'error' => $error,
                    'class' => get_class($exception),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'message' => $exception->getMessage(),
                    'stacktrace' => $exception->getTraceAsString()
                );
            }
        }

        if ($error == 'error-router-no-match') {
            $errorJson['message'] = 'Resource not found.';
        }
        $model = new JsonModel($errorJson);

        $e->setResult($model);

        return $model;
    }

    public function logErrors($event)
    {
        $exception = $event->getResult()->exception;
        if ($exception) {
            $sm = $event->getApplication()->getServiceManager();
            $service = $sm->get('Api/Logger');
            $service->err($exception);
        }
    }

    private function getBaseUrl($controller)
    {

        $basePath = $controller->getRequest()->getBasePath();

        $uri = new \Zend\Uri\Uri($controller->getRequest()->getUri());
        $uri->setPath($basePath);
        $uri->setQuery(array());
        $uri->setFragment('');

        return $uri->getScheme() . '://' . $uri->getHost() . '' . $uri->getPath();
    }

}
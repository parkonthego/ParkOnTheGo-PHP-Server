<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SwaggerController extends AbstractActionController
{

     protected $serviceLocator;
     function __construct(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
         $this->serviceLocator = $serviceLocator;
     }

     
    /**
     * Display the SwaggerUI interface
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $config = $this->serviceLocator->get('config');
        
        
        $helperManager = $this->serviceLocator->get('viewhelpermanager');
        $basePathHelper = $helperManager->get('basePath');
        
        // Default to /api/docs in case the configurable path is not set
        $swaggerUrl = '/api/docs';

        // Get swagger JSON url/path from config if set
        if (isset($config['swagger-ui']['swagger-json-url'])) {
            $swaggerUrl = $config['swagger-ui']['swagger-json-url'];
        }

        // Run through basePath helper if the string doesn't start with http
        if (substr($swaggerUrl, 0, 4) !== 'http') {
            $swaggerUrl = $basePathHelper->__invoke($swaggerUrl);
        }
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariable('swaggerUrl', $swaggerUrl);

        return $viewModel;
    }

}

<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api;

return array(
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\Index' => 'Api\Controller\IndexController',
        ),
        'factories' => array(
            'Api\Controller\Registration' => 'Api\ControllerFactory\RegistrationControllerFact',
            'Api\Controller\Authentication' => 'Api\ControllerFactory\AuthenticationControllerFact',
            'Api\Controller\Search' => 'Api\ControllerFactory\SearchControllerFact',
            'Api\Controller\Reservation' => 'Api\ControllerFactory\ReservationControllerFact',
        )
    ),
    'router' => array(
        'routes' => array(
            'api' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/api',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Api\Controller',
                        'controller' => 'Index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'auth' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/auth[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'userid' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Api\Controller\Authentication',
                            ),
                        ),
                    ),
                    'register' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/register[/:action]',
                            'defaults' => array(
                                'controller' => 'Api\Controller\Registration',
                                'action' => 'register',
                            ),
                        ),
                    ),
                    'password' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/password[/:action]',
                            'defaults' => array(
                                'controller' => 'Api\Controller\Password',
                                'action' => 'forgot',
                            ),
                        ),
                    ),
                    'users' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/users[/:id][/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'userid' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Api\Controller\Users',
                            ),
                        ),
                    ),
                    'search' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/search[/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Api\Controller\Search',
                            ),
                        ),
                    ),
                     'reservation' => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/reservation[/:id][/:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Api\Controller\Reservation',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);

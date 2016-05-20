<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Api\Controller;


use Zend\View\Model\JsonModel;
use Zend\Db\Sql\Expression;



class RegistrationController extends BaseRestfulJsonController {

   
     function __construct(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
         parent::__construct($serviceLocator);
     }

    public function registerAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $email = $this->getRequest()->getPost('email');
            $password = $this->getRequest()->getPost('password');
            $userName = $this->getRequest()->getPost('username');
            $firstName = $this->getRequest()->getPost('firstname');
            $lastName = $this->getRequest()->getPost('lastname');
            
            $user = new \Api\Model\User();
            $user->email = $email;
            $user->password = $password;
            $user->username = $userName;
            $user->first_name =$firstName;
            $user->last_name = $lastName;
            
            $userTable = $this->serviceLocator->get('Api\Model\UserTable');
            $userTable->insert($user);
            var_dump("Success");
            exit(0);
            
                    
           
        }

        $this->methodNotAllowed();
    }

}

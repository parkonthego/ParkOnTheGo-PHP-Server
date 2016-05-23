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
            $displayName = $this->getRequest()->getPost('firstname') . " " . $this->getRequest()->getPost('lastname');
            $firstName = $this->getRequest()->getPost('firstname');
            $lastName = $this->getRequest()->getPost('lastname');

            $user = new \Api\Model\User();
            $user->email = $email;
            $user->password = $password;
            $user->display_name = $displayName;
            $user->first_name = $firstName;
            $user->last_name = $lastName;

            try {
                $userTable = $this->serviceLocator->get('Api\Model\UserTable');
                $id = $userTable->insert($user);
                if($id == false){
                    throw new Exception("SQL Error");
                };
                $data = array(
                    'display_name' => $displayName,
                    'id' => $id
                );

                return $this->success($data);
            } catch (\Exception $e) {
                $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
                return $this->error($e->getMessage());
            }
        }

        $this->methodNotAllowed();
    }

}

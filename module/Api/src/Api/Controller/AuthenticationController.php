<?php

namespace Api\Controller;

use Zend\View\Model\JsonModel;
use Zend\Db\Sql\Expression;

class AuthenticationController extends BaseRestfulJsonController {

    function __construct(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        parent::__construct($serviceLocator);
    }

    public function loginAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $email = $this->getRequest()->getPost('email');
            $password = $this->getRequest()->getPost('password');

            try {
                $userTable = $this->serviceLocator->get('Api\Model\UserTable');
                $record = $userTable->fetchUser($email, $password);
                if (empty($record)) {
                    return $this->error("Invalid details");
                } else {
                    $data = $record;
                    return $this->success($data);
                }
            } catch (\Exception $e) {
                $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
                return false;
            }
        }

        $this->methodNotAllowed();
    }

}

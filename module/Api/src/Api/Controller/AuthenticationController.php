<?php

namespace Api\Controller;


use Zend\View\Model\JsonModel;
use Zend\Db\Sql\Expression;



class AuthenticationController extends BaseRestfulJsonController {

   
    public function loginAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array(
                'identity' => $this->getRequest()->getPost('login'),
                'credential' => $this->getRequest()->getPost('password')
            );
            $type = $this->getRequest()->getPost('type');
            $this->getRequest()->getPost()->set('identity', $data['identity']);
            $this->getRequest()->getPost()->set('credential', $data['credential']);

            $form = $this->getServiceLocator()->get('zfcuser_login_form');
            $form->setData($data);

            if (!$form->isValid()) {
                $this->flashMessenger()->setNamespace('zfcuser-login-form')->addMessage($this->failedLoginMessage);
                throw new \Api\Exception\ApiException($this->failedLoginMessage, 400);
            } else {
                $this->zfcUserAuthentication()->getAuthAdapter()->resetAdapters();
                $this->zfcUserAuthentication()->getAuthService()->clearIdentity();

                $adapter = $this->zfcUserAuthentication()->getAuthAdapter();

                $adapter->prepareForAuthentication($this->getRequest());
                $auth = $this->zfcUserAuthentication()->getAuthService()->authenticate($adapter);
                if (!$auth->isValid()) {
                    $adapter->resetAdapters();
                    throw new \Api\Exception\ApiException("Invalid email and password combination", 400);
                }

                if ($type === '2') {
                    $technicianTable = $this->getServiceLocator()->get('Api\Model\TechniciansTable');
                    $technician = $technicianTable->fetchRecordByAccountId($auth->getIdentity());
                    $data = array(
                        'technician_id' => $technician->id,
                        'name' => $technician->name
                    );
                } else {
                    $userTable = $this->getServiceLocator()->get('Api\Model\UserTable');
                    $user = $userTable->fetchRecordByAccountId($auth->getIdentity());
                    $data = array(
                        'user_id' => $user->id,
                        'name' => $user->name
                    );
                }

                return new JsonModel($data);
            }
        }

        $this->methodNotAllowed();
    }

}

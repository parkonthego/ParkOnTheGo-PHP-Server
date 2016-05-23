<?php

namespace Api\Controller;

use Zend\View\Model\JsonModel;
use Zend\Db\Sql\Expression;

class ReservationController extends BaseRestfulJsonController {

    function __construct(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        parent::__construct($serviceLocator);
    }

    //Override
    public function get($id) {
        try {
            if (!$id) {
                throw new \Api\Exception\ApiException("Invalid Data", 400);
            }

            $reservationTable = $this->serviceLocator->get('Api\Model\ReservationTable');
            $reservationDetails = $reservationTable->fetchRecord($id);
            if ($reservationDetails == false) {
                throw new Exception("SQL Error");
            };

            return $this->success($reservationDetails);
        } catch (Exception $ex) {
            $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->error($e->getMessage());
        }
    }

    public function getUserReservations() {

        try {
            $requestId = $this->params()->fromQuery('userId');
            $reservationTable = $this->serviceLocator->get('Api\Model\ReservationTable');
            $reservationDetails = $reservationTable->fetchUserServations($requestId);
           if ($reservationDetails == false) {
                throw new Exception("SQL Error");
            };

            return $this->success($reservationDetails);
        } catch (Exception $ex) {
              $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->error($e->getMessage());
        }
    }

    public function create() {
        $request = $this->getRequest();
        if ($request->isPost()) {


            $parkingId = $this->getRequest()->getPost('parkingid');
            $userId = $this->getRequest()->getPost('userid');
            $startingTime = $this->getRequest()->getPost('startingtime');
            $endTime = $this->getRequest()->getPost('endtime');

            $newReservation = new \Api\Model\Reservation();
            $newReservation->parking_id = $parkingId;
            $newReservation->user_id = $userId;
            $newReservation->starting_time = \Api\Utils\Functions\convertStringToDateTime($startingTime);
            $newReservation->end_time = \Api\Utils\Functions\convertStringToDateTime($endTime);

            try {
                $reservationTable = $this->serviceLocator->get('Api\Model\ReservationTable');
                $id = $reservationTable->insert($newReservation);
                if ($id == false) {
                    throw new Exception("SQL Error");
                };
                $data = array(
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

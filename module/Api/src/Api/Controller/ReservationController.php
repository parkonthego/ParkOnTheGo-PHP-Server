<?php

namespace Api\Controller;

use Zend\View\Model\JsonModel;
use Zend\Db\Sql\Expression;
use Api\Utils\Functions;

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

            if ($reservationDetails == NULL) {
                throw new \Api\Exception\ApiException("No data exist", 404);
            };

            if ($reservationDetails == false) {
                throw new \Api\Exception\ApiException("SQL Error", 500);
            };



            return $this->success($reservationDetails);
        } catch (Exception $ex) {
            $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->error($e->getMessage());
        }
    }

    public function getUserReservationsAction() {

        try {
            $requestId = $this->params()->fromQuery('userid');
            $reservationTable = $this->serviceLocator->get('Api\Model\ReservationTable');
            $reservationDetails = $reservationTable->fetchUserServations($requestId);
           
            if ($reservationDetails == -1) {
                throw new \Api\Exception\ApiException("No data exist", 404);
            };
            if ($reservationDetails == false) {
                throw new \Api\Exception\ApiException("SQL Error", 500);
            };

            return $this->success($reservationDetails);
        } catch (Exception $ex) {
            $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->error($e->getMessage());
        }
    }
    
    
     public function getUserPastReservationsAction() {

        try {
            $requestId = $this->params()->fromQuery('userid');
            $reservationTable = $this->serviceLocator->get('Api\Model\ReservationTable');
            $reservationDetails = $reservationTable->fetchUserPastServations($requestId);
            if ($reservationDetails == false) {
                throw new \Api\Exception\ApiException("SQL Error", 500);
            };
            if ($reservationDetails == NULL) {
                throw new \Api\Exception\ApiException("No data exist", 404);
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
            $cost = $this->getRequest()->getPost('cost');

            $newReservation = new \Api\Model\Reservation();
            $newReservation->parking_id = $parkingId;
            $newReservation->user_id = $userId;
            $newReservation->starting_time = $this->convertStringToDateTime($startingTime);
            $newReservation->end_time = $this->convertStringToDateTime($endTime);
            $newReservation->cost = $cost;
            $newReservation->status = true;

            $reservationTable = $this->serviceLocator->get('Api\Model\ReservationTable');

            $result = $reservationTable->isReservationConflict($newReservation->starting_time, $newReservation->end_time);

            if ($result != NULL) {
                return $this->error("Reservation has conflict with other reservation");
            }


            try {

                $id = $reservationTable->insert($newReservation);
                if ($id == false) {
                    throw new \Api\Exception\ApiException("Reservation is already exists", 500);
                };
                if ($id == NULL) {
                    throw new \Api\Exception\ApiException("No data exist", 404);
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

    public function editAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {


            $reservationId = $this->getRequest()->getPost('reservationid');
            $parkingId = $this->getRequest()->getPost('parkingid');
            $userId = $this->getRequest()->getPost('userid');
            $startingTime = $this->getRequest()->getPost('startingtime');
            $endTime = $this->getRequest()->getPost('endtime');
            $cost = $this->getRequest()->getPost('cost');

            $newReservation = new \Api\Model\Reservation();
            $newReservation->id = $reservationId;
            $newReservation->parking_id = $parkingId;
            $newReservation->user_id = $userId;
            $newReservation->starting_time = $this->convertStringToDateTime($startingTime);
            $newReservation->end_time = $this->convertStringToDateTime($endTime);
            $newReservation->cost = $cost;
            $newReservation->status = true;

            $reservationTable = $this->serviceLocator->get('Api\Model\ReservationTable');


            $result = $reservationTable->isReservationConflict($newReservation->starting_time, $newReservation->end_time);

            if ($result != NULL) {
                return $this->error("Reservation has conflict with other reservation");
            }


            try {
                $id = $reservationTable->update($newReservation);
                if ($id == false) {
                    throw new \Api\Exception\ApiException("Reservation can not be done, there another reservation at the same time", 500);
                };
                if ($id == NULL) {
                    throw new \Api\Exception\ApiException("No data exist", 404);
                };
                if ($id == false) {
                    return $this->error("Reservation can not be done, there another reservation at the same time");
                } else {

                    return $this->success();
                }
            } catch (\Exception $e) {
                $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
                return $this->error($e->getMessage());
            }
        }

        $this->methodNotAllowed();
    }

    public function delete($id) {



        $reservationId = $id;
        $newReservation = new \Api\Model\Reservation();
        $newReservation->id = $reservationId;
        $newReservation->status = false;

        try {
            $reservationTable = $this->serviceLocator->get('Api\Model\ReservationTable');
            $id = $reservationTable->deleteReservation($newReservation);
            if ($id == false) {
                throw new \Api\Exception\ApiException("Reservation can not be deleted", 500);
            };
            if ($id == NULL) {
                throw new \Api\Exception\ApiException("No data exist", 404);
            };
            if ($id == false) {
                return $this->error("eservation can not be deleted");
            } else {

                return $this->success();
            }
        } catch (\Exception $e) {
            $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->error($e->getMessage());
        }
    }

}

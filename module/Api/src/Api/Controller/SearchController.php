<?php

namespace Api\Controller;

use Zend\View\Model\JsonModel;
use Zend\Db\Sql\Expression;

class SearchController extends BaseRestfulJsonController {

    function __construct(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        parent::__construct($serviceLocator);
    }

    //Override
    public function get($id) {
        try {
            if (!$id) {
                throw new \Api\Exception\ApiException("Invalid Data", 400);
            }

            $parkingSlotTable = $this->serviceLocator->get('Api\Model\ParkingSlotTable');
            $locationDetail = $parkingSlotTable->fetchRecord($id);
            if ($locationDetail == false) {
                throw new Exception("SQL Error");
            };
            return $this->success($locationDetail);
        } catch (Exception $ex) {
            $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->error($e->getMessage());
        }
    }

    public function getLocationsNearMeAction() {
        try {
           
            $userId = $this->params()->fromQuery('userId');
            $lat = $this->params()->fromQuery('lat');
            $long = $this->params()->fromQuery('long');
            $distance = $this->params()->fromQuery('dis');
            $parkingSlotTable = $this->serviceLocator->get('Api\Model\ParkingSlotTable');
           // $searchDetails = $parkingSlotTable->getLocationsTo(37.3471218,-121.931602,20);
            $searchDetails = $parkingSlotTable->getLocationsTo($lat,$long,$distance);
            if ($searchDetails == false) {
                throw new Exception("SQL Error");
            };
            return $this->success($searchDetails);
        } catch (Exception $ex) {
            $this->logger->ERR($e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->error($e->getMessage());
        }
    }

}

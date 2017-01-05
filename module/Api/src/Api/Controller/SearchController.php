<?php

namespace Api\Controller;

use Swagger\Annotations as SWG;
use Zend\View\Model\JsonModel;
use Zend\Db\Sql\Expression;

/**
 * @SWG\Info(title="My First API", version="0.1")
 */
class SearchController extends BaseRestfulJsonController {

    function __construct(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        parent::__construct($serviceLocator);
    }

    //Override
    /**
     * @SWG\Get(
     *     path="/api/resource.json",
     *     @SWG\Response(response="200", description="An example resource")
     * )
     */
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
            $startTime = $this->params()->fromQuery('startdatetime');
            $endTime = $this->params()->fromQuery('enddatetime');
            $this->logger->INFO('SD' . $startTime);
            $this->logger->INFO('ED' . $endTime);
            $startTime = $this->convertStringToSqlDateTime($startTime);
            $endTime = $this->convertStringToSqlDateTime($endTime);
            $this->logger->INFO('CSD' . $startTime);
            $this->logger->INFO('CED' . $endTime);
            $parkingSlotTable = $this->serviceLocator->get('Api\Model\ParkingSlotTable');
            // $searchDetails = $parkingSlotTable->getLocationsTo(37.3471218,-121.931602,20);
            $searchDetails = $parkingSlotTable->getLocationsTo($lat, $long, $distance, $startTime, $endTime);
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

<?php

namespace Api\Model;

class ParkingSlotTable extends BaseModelTable {

    public function insert(ParkingSlot $parkingSlot) {
        try {
            $this->created($parkingSlot);
            // Need to write to vaidations
            if (!$this->isValid($parkingSlot)) {
                return false;
            }
            $this->tableGateway->insert($parkingSlot->getArrayCopy());
            $filedId = $this->tableGateway->getLastInsertValue();
            return $filedId;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function update(ParkingSlot $parkingSlot) {
        try {
            $this->updated($parkingSlot);

            // Need to write to vaidations
            if (!$this->isValid($parkingSlot)) {
                return false;
            }

            $data = array_filter($parkingSlot->getArrayCopy());

            $this->tableGateway->update($data, array('id' => $parkingSlot->id));

            return true;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();

        $result = array();
        foreach ($resultSet as $projectRow) {
            $result[] = $projectRow;
        }

        if ($result) {

            return $result;
        }
    }

    public function fetchRecord($requestId) {
        $rowset = $this->tableGateway->select(array('id' => $requestId));
        $artistRow = $rowset->current();

        return $artistRow;
    }

    public function getLocationsTo($lat, $long, $radius) {

        $filter = new \Zend\Db\Sql\Predicate\Predicate();
        $filter->lessThanOrEqualTo('distance', $radius);
        $distance = new \Zend\Db\Sql\Expression("3956*2*ASIN(SQRT(POWER(SIN((" . $lat . " - latitude)*pi()/180 / 2), 2) + COS(" . $lat . " * pi() / 180) * COS(latitude * pi()/180) * POWER(SIN((" . $long . " - longitude)*pi()/180 / 2),2)))");
        $userFields = array('id', 'latitude', 'longitude', 'description', 'price', 'distance' => $distance);
        $select = new \Zend\Db\Sql\Select;
        $select->from(array('ps' => 'parking_slot'))
                ->columns($userFields)
                ->having($filter)
                ->order(array('distance' => 'ASC'))
                ->limit(20)
                ->offset(0);
        
       
        $statement = $this->getSql()->prepareStatementForSqlObject($select);
        $resultSet = $statement->execute();

        $result = array();
        foreach ($resultSet as $projectRow) {
            $result[] = $projectRow;
        }

        if ($result) {

            return $result;
        }
    }

}

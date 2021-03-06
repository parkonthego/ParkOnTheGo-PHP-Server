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

        try {


            $filter = new \Zend\Db\Sql\Predicate\Predicate();
            $filter->lessThanOrEqualTo("starting_time", new \Zend\Db\Sql\Expression('NOW()'))->and->greaterThan("end_time", new \Zend\Db\Sql\Expression('NOW()'))->and->equalTo("parking_id", $requestId)->and->equalTo("status", true);
            $statusSelect = new \Zend\Db\Sql\Select();
            $statusSelect->from(array('r' => 'reservation'))
                    ->columns(array("number" => new \Zend\Db\Sql\Expression("Count(*)")))
                    ->where($filter);

            $statement = $this->getSql()->prepareStatementForSqlObject($statusSelect);
            $resultSet = $statement->execute();

            $staus = false;
            foreach ($resultSet as $projectRow) {
                if (array_key_exists('number', $projectRow)) {
                    if ($projectRow['number'] > 0)
                        $staus = true;
                }
            }


            $select = new \Zend\Db\Sql\Select;
            $select->from(array('ps' => 'parking_slot'))
                    ->columns(array("*"))
                    ->where(array("id" => $requestId));

            $statement = $this->getSql()->prepareStatementForSqlObject($select);
            $resultSet = $statement->execute();

            $result = array();
            foreach ($resultSet as $projectRow) {
                $projectRow['status'] = $staus;
                $result[] = $projectRow;
            }

            if ($result) {

                return $result[0];
            }
        } catch (Exception $ex) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function getLocationsTo($lat, $long, $radius, $statingTime, $endTime) {


        try {

            $subFilter = new \Zend\Db\Sql\Predicate\Predicate();
            $subFilter->nest()
                    ->nest()
                    ->greaterThanOrEqualTo('r.starting_time', $statingTime)
                    ->and
                    ->lessThanOrEqualTo('r.starting_time', $endTime)
                    ->and->equalTo("status", true)
                    ->unnest()
                    ->or
                    ->nest()
                    ->greaterThanOrEqualTo('r.end_time', $statingTime)
                    ->and
                    ->lessThanOrEqualTo('r.end_time', $endTime)
                    ->and->equalTo("status", true)
                    ->unnest()
                    ->or
                    ->nest()
                    ->greaterThanOrEqualTo('r.starting_time', $statingTime)
                    ->AND
                    ->lessThanOrEqualTo('r.end_time', $endTime)
                    ->and->equalTo("status", true)
                    ->unnest()
                    ->or
                    ->nest()
                    ->lessThanOrEqualTo('r.starting_time', $statingTime)
                    ->AND
                    ->greaterThanOrEqualTo('r.end_time', $endTime)
                    ->and->equalTo("status", true)
                    ->unnest()
                    ->unnest();





            $subSelect = new \Zend\Db\Sql\Select;
            $subSelect->from(array('r' => 'reservation'))
                    ->columns(array('parking_id' => new \Zend\Db\Sql\Expression("distinct parking_id")))
                    ->where($subFilter);
          
            $filter = new \Zend\Db\Sql\Predicate\Predicate();
            $filter->lessThanOrEqualTo('distance', $radius);
            $whereFilter = new \Zend\Db\Sql\Predicate\Predicate();
            $whereFilter->addPredicate(new \Zend\Db\Sql\Predicate\Expression('ps.id NOT IN (?)', array($subSelect)));
            $distance = new \Zend\Db\Sql\Expression("3956*2*ASIN(SQRT(POWER(SIN((" . $lat . " - latitude)*pi()/180 / 2), 2) + COS(" . $lat . " * pi() / 180) * COS(latitude * pi()/180) * POWER(SIN((" . $long . " - longitude)*pi()/180 / 2),2)))");
            $userFields = array('id', 'latitude', 'longitude', 'description', 'price', 'distance' => $distance);
            $select = new \Zend\Db\Sql\Select;
            $select->from(array('ps' => 'parking_slot'))
                    ->columns($userFields)
                    ->where($whereFilter)
                    ->having($filter)
                    ->order(array('distance' => 'ASC'))
                    ->limit(40)
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
        } catch (Exception $ex) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

}

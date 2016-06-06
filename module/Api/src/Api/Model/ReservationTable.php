<?php

namespace Api\Model;

class ReservationTable extends BaseModelTable {

    public function insert(Reservation $reservation) {
        try {

            $this->created($reservation);
            // Need to write to vaidations
            if (!$this->isValid($reservation)) {
                return false;
            }

            $this->tableGateway->insert($reservation->getArrayCopy());
            $filedId = $this->tableGateway->getLastInsertValue();
            return $filedId;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function update(Reservation $reservation) {
        try {
            $this->updated($reservation);

            // Need to write to vaidations
            if (!$this->isValid($reservation)) {
                return false;
            }

            $data = array_filter($reservation->getArrayCopy());

            $this->tableGateway->update($data, array('id' => $reservation->id));

            return true;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function fetchRecord($requestId) {
        try {

            $filter = new \Zend\Db\Sql\Predicate\Predicate();
            $filter->greaterThanOrEqualTo("end_time", new \Zend\Db\Sql\Expression("NOW()"))->and->equalTo("id", $requestId)->and->equalTo("status", true);
            $rowset = $this->tableGateway->select($filter);
            $artistRow = $rowset->current();
            return $artistRow;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function fetchUserServations($id) {
        try {
            $select = new \Zend\Db\Sql\Select;

            $filter = new \Zend\Db\Sql\Predicate\Predicate();
            $filter->greaterThanOrEqualTo("r.end_time", new \Zend\Db\Sql\Expression("NOW()"))->and->equalTo("r.user_id", $id)->and->equalTo("r.status", true);

            $select->from(array('r' => 'reservation'))
                    ->join(array('p' => 'parking_slot'), 'r.parking_id = p.id', array('latitude', 'longitude', 'description', 'price'))
                    ->where($filter)
                    ->columns(array('*'));

            $statement = $this->getSql()->prepareStatementForSqlObject($select);

            $data = $statement->execute();
            $result = array();
            foreach ($data as $projectRow) {

                $timestamp = strtotime($projectRow['end_time']);
                $projectRow['end_time'] = date('m/d/Y H:i', $timestamp);
                $timestamp = strtotime($projectRow['starting_time']);
                $projectRow['starting_time'] = date('m/d/Y H:i', $timestamp);

                $result[] = $projectRow;
            }

            if ($result) {
                return $result;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function fetchUserPastServations($id) {
        try {
            $select = new \Zend\Db\Sql\Select;

            $filter = new \Zend\Db\Sql\Predicate\Predicate();
            $filter->lessThan("r.end_time", new \Zend\Db\Sql\Expression("NOW()"))->and->equalTo("r.user_id", $id)->and->equalTo("r.status", true);

            $select->from(array('r' => 'reservation'))
                    ->join(array('p' => 'parking_slot'), 'r.parking_id = p.id', array('latitude', 'longitude', 'description', 'price'))
                    ->where($filter)
                    ->columns(array('*'));



            $statement = $this->getSql()->prepareStatementForSqlObject($select);

            $data = $statement->execute();
            $result = array();
            foreach ($data as $projectRow) {
                $timestamp = strtotime($projectRow['end_time']);
                $projectRow['end_time'] = date('m/d/Y H:i', $timestamp);
                $timestamp = strtotime($projectRow['starting_time']);
                $projectRow['starting_time'] = date('m/d/Y H:i', $timestamp);

                $result[] = $projectRow;
            }

            if ($result) {

                return $result;
            } else {
                return -1;
            }
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function deleteReservation(Reservation $reservation) {
        try {
            $this->updated($reservation);

            // Need to write to vaidations
            if (!$this->isValid($reservation)) {
                return false;
            }


            $data = array_filter($reservation->getArrayCopy());

            if ($reservation->status == false) {
                $data['status'] = false;
            }

            $this->tableGateway->update($data, array('id' => $reservation->id));

            return true;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function isReservationConflict($startTime, $endTime, $parkingId) {

        $subFilter = new \Zend\Db\Sql\Predicate\Predicate();
        $subFilter = new \Zend\Db\Sql\Predicate\Predicate();
        $subFilter->nest()
                ->nest()
                ->greaterThanOrEqualTo('r.starting_time', $startTime)
                ->and
                ->lessThanOrEqualTo('r.starting_time', $endTime)
                ->and->equalTo("status", true)->and->equalTo('r.parking_id', $parkingId)
                ->unnest()
                ->or
                ->nest()
                ->greaterThanOrEqualTo('r.end_time', $startTime)
                ->and
                ->lessThanOrEqualTo('r.end_time', $endTime)
                ->and->equalTo("status", true)->and->equalTo('r.parking_id', $parkingId)
                ->unnest()
                ->or
                ->nest()
                ->greaterThanOrEqualTo('r.starting_time', $startTime)
                ->AND
                ->lessThanOrEqualTo('r.end_time', $endTime)
                ->and->equalTo("status", true)->and->equalTo('r.parking_id', $parkingId)
                ->unnest()
                ->or
                ->nest()
                ->lessThanOrEqualTo('r.starting_time', $startTime)
                ->AND
                ->greaterThanOrEqualTo('r.end_time', $endTime)
                ->and->equalTo("status", true)
                ->unnest()
                ->unnest();

        $subSelect = new \Zend\Db\Sql\Select;
        $subSelect->from(array('r' => 'reservation'))
                ->join(array('p' => 'parking_slot'), 'r.parking_id = p.id', array())
                ->columns(array('id'))
                ->where($subFilter);


        //print_r($subSelect->getSqlString());
        $statement = $this->getSql()->prepareStatementForSqlObject($subSelect);
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

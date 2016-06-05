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
            $rowset = $this->tableGateway->select(array('id' => $requestId, 'status' => true));
            $artistRow = $rowset->current();
            return $artistRow;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function fetchUserServations($id) {
        $select = new \Zend\Db\Sql\Select;

        $select->from(array('r' => 'reservation'))
                ->join(array('p' => 'parking_slot'), 'r.parking_id = p.id', array('*'))
                ->where(array('r.user_id' => $id, 'r.status' => true))
                ->columns(array('*'));
        
        
        $statement = $this->getSql()->prepareStatementForSqlObject($select);

        $data = $statement->execute();
        $result = array();
        foreach ($data as $projectRow) {
            $result[] = $projectRow;
        }

        if ($result) {

            return $result;
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
            
            if($reservation->status == false){
                $data['status'] = false;
            }
            
            $this->tableGateway->update($data, array('id' => $reservation->id));

            return true;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }
    
    public function isReservationConflict($startTime,$endTime){
        
        $subFilter = new \Zend\Db\Sql\Predicate\Predicate();
        $subFilter->lessThanOrEqualTo("r.starting_time", $startTime)->and->greaterThan("r.end_time", $endTime);

        $subSelect = new \Zend\Db\Sql\Select;
        $subSelect->from(array('r' => 'reservation'))
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

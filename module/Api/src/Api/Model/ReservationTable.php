<?php

namespace Api\Model;

class ParkingSlotTable extends BaseModelTable {

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
        $rowset = $this->tableGateway->select(array('id' => $requestId));
        $artistRow = $rowset->current();

        return $artistRow;
    }

    public function fetchUserServations($id) {
        $select = new \Zend\Db\Sql\Select;

        $select->from(array('r' => 'reservation'))
                ->join(array('p' => 'parking_slot'), 'r.parking_id = p.id', array('*'))
                ->where(array('uo.user_id' => $userId))
                ->where(array('o.api_key' => $apiKey))
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

}

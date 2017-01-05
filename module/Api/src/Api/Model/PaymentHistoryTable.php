<?php

namespace Api\Model;

class PaymentHistoryTable extends BaseModelTable {

    public function insert(PaymentHistory $paymentHistory) {
        try {
            $this->created($paymentHistory);
            // Need to write to vaidations
            if (!$this->isValid($paymentHistory)) {
                return false;
            }
            $this->tableGateway->insert($paymentHistory->getArrayCopy());
            $filedId = $this->tableGateway->getLastInsertValue();
            return $filedId;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function update(PaymentHistory $paymentHistory) {
        try {
            $this->updated($paymentHistory);

            // Need to write to vaidations
            if (!$this->isValid($paymentHistory)) {
                return false;
            }

            $data = array_filter($paymentHistory->getArrayCopy());

            $this->tableGateway->update($data, array('id' => $paymentHistory->id));

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

    

}

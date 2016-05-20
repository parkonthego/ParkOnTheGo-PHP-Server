<?php

namespace Api\Model;

class UserTable extends BaseModelTable
    {

    public function insert(User $user)
    {
        try {
            $this->created($user);
            // Need to write to vaidations
            if (!$this->isValid($user)) {
                return false;
            }
            $this->tableGateway->insert($user->getArrayCopy());
            $filedId = $this->tableGateway->getLastInsertValue();
            return $filedId;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function update(User $user)
    {
        try {
            $this->updated($user);

            // Need to write to vaidations
            if (!$this->isValid($user)) {
                return false;
            }

            $data = array_filter($user->getArrayCopy());

            $this->tableGateway->update($data, array('user_id' => $user->id));

            return true;
        } catch (\Exception $e) {
            $this->logger->err($e->getMessage());
            return false;
        }
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    }

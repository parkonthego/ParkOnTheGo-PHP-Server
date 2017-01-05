<?php

namespace Api\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class BaseModel implements InputFilterAwareInterface
    {

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        return null;
    }

    public function exchangeArray($data)
    {
        foreach ($this->getArrayCopy() as $key => $value) {
            $this->$key = (isset($data[$key])) ? $data[$key] : null;
        }
    }

    public function isValid()
    {
        $filter = $this->getInputFilter();
        if ($filter) {
            $filter->setData($this->getArrayCopy());
            if ($filter->isValid()) {
                return true;
            } else {
                return $filter->getMessages();
            }
        }

        // if no filters are present currently sending true
        return true;
    }

    }

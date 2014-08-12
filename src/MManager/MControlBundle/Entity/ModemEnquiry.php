<?php

namespace MManager\MControlBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;

class ModemEnquiry {
    protected $modemLocation;
    protected $modemSerial;
    protected $modemPhone;
    protected $modemGroup;
    public $schedule;
    
    public function getModemId()
    {
        return $this->modemId;
    }

    public function setModemId($modemId)
    {
        $this->modemId = $modemId;
    }

    public function getModemLocation()
    {
        return $this->modemLocation;
    }

    public function setModemLocation($modemLocation)
    {
        $this->modemLocation = $modemLocation;
    }

    public function getModemSerial()
    {
        return $this->modemSerial;
    }

    public function setModemSerial($modemSerial)
    {
        $this->modemSerial = $modemSerial;
    }

    public function getModemPhone()
    {
        return $this->modemPhone;
    }

    public function setModemPhone($modemPhone)
    {
        $this->modemPhone = $modemPhone;
    }

    public function getModemGroup()
    {
        return $this->modemGroup;
    }

    public function setModemGroup($modemGroup)
    {
        $this->modemGroup = $modemGroup;
    }    
    
    public function getModemSchedule()
    {
        return $this->schedule;
    }
    
    public function setModemSchedule($schedule)
    {
        $this->schedule = $schedule;
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('modemPhone', new NotBlank());      
        $metadata->addPropertyConstraint('modemLocation', new NotBlank());
    }
}


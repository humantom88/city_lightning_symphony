<?php

namespace MManager\MControlBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;

class TimeblockEnquiry {
    protected $timeblock_id;
    protected $timeblock_date;
    protected $timeblock_starttime;
    protected $timeblock_endtime;
    protected $schedule;
    
    public function getTimeblockId()
    {
        return $this->timeblockId;
    }

    public function setTimeblockId($timeblockId)
    {
        $this->timeblockId = $timeblockId;
    }

    public function getTimeblockDate()
    {
        return $this->timeblock_date;
    }

    public function setTimeblockDate($timeblock_date)
    {
        $this->$timeblock_date = $timeblock_date;
    }

    public function getTimeblockStarttime()
    {
        return $this->timeblock_starttime;
    }

    public function setTimeblockStarttime($timeblock_starttime)
    {
        $this->timeblock_starttime = $timeblock_starttime;
    }

    public function setTimeblockEndtime($timeblock_endtime)
    {
        $this->timeblock_endtime = $timeblock_endtime;
    }

    public function getTimeblockEndtime()
    {
        return $this->timeblock_endtime;
    }
    
    public function getSchedule()
    {
        return $this->schedule;
    }
    
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }
    
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('timeblock_date', new NotBlank());      
        $metadata->addPropertyConstraint('timeblock_starttime', new NotBlank());
        $metadata->addPropertyConstraint('timeblock_endtime', new NotBlank());
    }
}


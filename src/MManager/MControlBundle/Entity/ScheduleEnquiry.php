<?php

namespace MManager\MControlBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;

class ScheduleEnquiry {
    protected $scheduleName;
    protected $scheduleId;
    protected $schedulePhone;
    protected $scheduleGroup;
            
    public function getScheduleId()
    {
        return $this->scheduleId;
    }

    public function setScheduleId($scheduleId)
    {
        $this->scheduleId = $scheduleId;
    }

    public function getScheduleName()
    {
        return $this->scheduleName;
    }

    public function setScheduleName($scheduleName)
    {
        $this->scheduleName = $scheduleName;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('scheduleName', new NotBlank());
    }
}


<?php

namespace MManager\MControlBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;

class ModemGroupEnquiry {
    protected $modemGroupName;
            
    public function getModemGroupName()
    {
        return $this->modemGroupName;
    }

    public function setModemGroupName($modemGroupName)
    {
        $this->modemGroupName = $modemGroupName;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('modemGroupName', new NotBlank());      
    }
}


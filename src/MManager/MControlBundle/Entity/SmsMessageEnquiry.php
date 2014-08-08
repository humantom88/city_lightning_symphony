<?php
// src/Blogger/BlogBundle/Entity/Blog.php

namespace MManager\MControlBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;

class SmsMessage
{
    protected $sms_id;
    protected $modem_id;
    protected $sms_text;
    protected $sms_from;
    protected $sms_sentdate;

    public function getSmsId()
    {
        return $this->sms_id;
    }

    public function setSmsText($smsText)
    {
        $this->sms_text = $smsText;

        return $this;
    }

    public function getSmsText()
    {
        return $this->sms_text;
    }

    public function setSmsFrom($smsFrom)
    {
        $this->sms_from = $smsFrom;

        return $this;
    }

    public function getSmsFrom()
    {
        return $this->sms_from;
    }

    public function setSmsSentdate($smsSentdate)
    {
        $this->sms_sentdate = $smsSentdate;

        return $this;
    }

    public function getSmsSentdate()
    {
        return $this->sms_sentdate;
    }

    public function setModemId(\MManager\MControlBundle\Entity\Modem $modemId = null)
    {
        $this->modem_id = $modemId;

        return $this;
    }

    public function getModemId()
    {
        return $this->modem_id;
    }
}

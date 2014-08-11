<?php
// src/Blogger/BlogBundle/Entity/Blog.php

namespace MManager\MControlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="smsmessages")
 */
class SmsMessage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $sms_id;

    /**
     * @ORM\ManyToOne(targetEntity="Modem", inversedBy="smsmessage")
     * @ORM\JoinColumn(name="modem_id", referencedColumnName="modem_id")
     */
    protected $modem_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $sms_text;

    /**
     * @ORM\Column(type="string", length=20)
     */
    protected $sms_from;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $sms_sentdate;

    /**
     * Get sms_id
     *
     * @return integer 
     */
    public function getSmsId()
    {
        return $this->sms_id;
    }

    /**
     * Set sms_text
     *
     * @param string $smsText
     * @return SmsMessage
     */
    public function setSmsText($smsText)
    {
        $this->sms_text = $smsText;

        return $this;
    }

    /**
     * Get sms_text
     *
     * @return string 
     */
    public function getSmsText()
    {
        return $this->sms_text;
    }

    /**
     * Set sms_from
     *
     * @param string $smsFrom
     * @return SmsMessage
     */
    public function setSmsFrom($smsFrom)
    {
        $this->sms_from = $smsFrom;

        return $this;
    }

    /**
     * Get sms_from
     *
     * @return string 
     */
    public function getSmsFrom()
    {
        return $this->sms_from;
    }

    /**
     * Set sms_sentdate
     *
     * @param \DateTime $smsSentdate
     * @return SmsMessage
     */
    public function setSmsSentdate($smsSentdate)
    {
        $this->sms_sentdate = $smsSentdate;

        return $this;
    }

    /**
     * Get sms_sentdate
     *
     * @return \DateTime 
     */
    public function getSmsSentdate()
    {
        return $this->sms_sentdate;
    }

    /**
     * Set modem_id
     *
     * @param \MManager\MControlBundle\Entity\Modem $modemId
     * @return SmsMessage
     */
    public function setModemId(\MManager\MControlBundle\Entity\Modem $modemId = null)
    {
        $this->modem_id = $modemId;

        return $this;
    }

    /**
     * Get modem_id
     *
     * @return \MManager\MControlBundle\Entity\Modem 
     */
    public function getModemId()
    {
        return $this->modem_id;
    }
}

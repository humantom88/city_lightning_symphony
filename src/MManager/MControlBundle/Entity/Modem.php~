<?php
// src/Blogger/BlogBundle/Entity/Blog.php

namespace MManager\MControlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="modems")
 */
class Modem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $modem_id;

    /**
     * @ORM\ManyToOne(targetEntity="ModemGroup", inversedBy="modems")
     * @ORM\JoinColumn(name="modem_group_id", referencedColumnName="modemgroup_id")
     */
    protected $modem_group_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $modem_location;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $modem_serial;

    /**
     * @ORM\Column(type="text")
     */
    protected $modem_phone;

    /**
     * Get modem_id
     *
     * @return integer 
     */
    public function getModemId()
    {
        return $this->modem_id;
    }

    /**
     * Set modem_location
     *
     * @param string $modemLocation
     * @return Modem
     */
    public function setModemLocation($modemLocation)
    {
        $this->modem_location = $modemLocation;

        return $this;
    }

    /**
     * Get modem_location
     *
     * @return string 
     */
    public function getModemLocation()
    {
        return $this->modem_location;
    }

    /**
     * Set modem_serial
     *
     * @param string $modemSerial
     * @return Modem
     */
    public function setModemSerial($modemSerial)
    {
        $this->modem_serial = $modemSerial;

        return $this;
    }

    /**
     * Get modem_serial
     *
     * @return string 
     */
    public function getModemSerial()
    {
        return $this->modem_serial;
    }

    /**
     * Set modem_phone
     *
     * @param string $modemPhone
     * @return Modem
     */
    public function setModemPhone($modemPhone)
    {
        $this->modem_phone = $modemPhone;
        return $this;
    }

    /**
     * Get modem_phone
     *
     * @return string 
     */
    public function getModemPhone()
    {
        return $this->modem_phone;
    }
    
    public function getModemAsArray() 
    {
        $arr = [
            'modem_id' => $this->getModemId(), 
            'modem_phone' => $this->getModemPhone(), 
            'modem_serial' => $this->getModemSerial(), 
            'modem_location' => $this->getModemLocation()
        ];
        return $arr;
    }

    /**
     * Set modem_group_id
     *
     * @param \MManager\MControlBundle\Entity\ModemGroup $modemGroupId
     * @return Modem
     */
    public function setModemGroupId(\MManager\MControlBundle\Entity\ModemGroup $modemGroupId = null)
    {
        $this->modem_group_id = $modemGroupId;

        return $this;
    }

    /**
     * Get modem_group_id
     *
     * @return \MManager\MControlBundle\Entity\ModemGroup 
     */
    public function getModemGroupId()
    {
        return $this->modem_group_id;
    }
}

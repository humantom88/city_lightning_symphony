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
}

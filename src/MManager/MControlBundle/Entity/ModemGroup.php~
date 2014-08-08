<?php
// src/Blogger/BlogBundle/Entity/Blog.php

namespace MManager\MControlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="modem_groups")
 */
class ModemGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $modemgroup_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $modemgroup_name;

    /**
     * @ORM\OneToMany(targetEntity="Modem", mappedBy="modem_id")
     */
    protected $modems;

    public function __construct()
    {
        $this->modems = new ArrayCollection();
    }

    /**
     * Get modemgroup_id
     *
     * @return integer 
     */
    public function getModemgroupId()
    {
        return $this->modemgroup_id;
    }

    /**
     * Set modemgroup_name
     *
     * @param string $modemgroupName
     * @return ModemGroup
     */
    public function setModemgroupName($modemgroupName)
    {
        $this->modemgroup_name = $modemgroupName;

        return $this;
    }

    /**
     * Get modemgroup_name
     *
     * @return string 
     */
    public function getModemgroupName()
    {
        return $this->modemgroup_name;
    }

    /**
     * Add modems
     *
     * @param \MManager\MControlBundle\Entity\Modem $modems
     * @return ModemGroup
     */
    public function addModem(\MManager\MControlBundle\Entity\Modem $modems)
    {
        $this->modems[] = $modems;

        return $this;
    }

    /**
     * Remove modems
     *
     * @param \MManager\MControlBundle\Entity\Modem $modems
     */
    public function removeModem(\MManager\MControlBundle\Entity\Modem $modems)
    {
        $this->modems->removeElement($modems);
    }

    /**
     * Get modems
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModems()
    {
        return $this->modems;
    }
}

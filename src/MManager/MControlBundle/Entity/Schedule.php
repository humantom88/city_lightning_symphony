<?php
namespace MManager\MControlBundle\Entity;

use MManager\MControlBundle\Controller\ModemController;
use MManager\MControlBundle\Entity\Modem;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="schedules")
 */
class Schedule
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $schedule_id;

    /**
     * @ORM\OneToMany(targetEntity="Timeblock", mappedBy="schedule_id")
     */
    protected $timeblocks;

    /**
     * @ORM\Column(type="string")
     */
    protected $schedule_name;

    /**
     * @ORM\OneToMany(targetEntity="Modem", mappedBy="modem_id")
     */
    protected $modem_id;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->timeblocks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->modem_id = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get schedule_id
     *
     * @return integer 
     */
    public function getScheduleId()
    {
        return $this->schedule_id;
    }

    /**
     * Set schedule_name
     *
     * @param string $scheduleName
     * @return Schedule
     */
    public function setScheduleName($scheduleName)
    {
        $this->schedule_name = $scheduleName;

        return $this;
    }

    /**
     * Get schedule_name
     *
     * @return string 
     */
    public function getScheduleName()
    {
        return $this->schedule_name;
    }

    /**
     * Add timeblocks
     *
     * @param \MManager\MControlBundle\Entity\Timeblock $timeblocks
     * @return Schedule
     */
    public function addTimeblock(\MManager\MControlBundle\Entity\Timeblock $timeblocks)
    {
        $this->timeblocks[] = $timeblocks;

        return $this;
    }

    /**
     * Remove timeblocks
     *
     * @param \MManager\MControlBundle\Entity\Timeblock $timeblocks
     */
    public function removeTimeblock(\MManager\MControlBundle\Entity\Timeblock $timeblocks)
    {
        $this->timeblocks->removeElement($timeblocks);
    }

    /**
     * Get timeblocks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTimeblocks()
    {
        return $this->timeblocks;
    }

    /**
     * Add modem_id
     *
     * @param \MManager\MControlBundle\Entity\Modem $modemId
     * @return Schedule
     */
    public function addModemId(\MManager\MControlBundle\Entity\Modem $modemId)
    {
        $this->modem_id[] = $modemId;

        return $this;
    }

    /**
     * Remove modem_id
     *
     * @param \MManager\MControlBundle\Entity\Modem $modemId
     */
    public function removeModemId(\MManager\MControlBundle\Entity\Modem $modemId)
    {
        $this->modem_id->removeElement($modemId);
    }

    /**
     * Get modem_id
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModemId()
    {
        return $this->modem_id;
    }
}

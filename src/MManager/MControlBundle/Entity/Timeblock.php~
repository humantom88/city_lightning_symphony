<?php
// src/Blogger/BlogBundle/Entity/Blog.php

namespace MManager\MControlBundle\Entity;

use MManager\MControlBundle\Controller\ModemController;
use MManager\MControlBundle\Entity\Schedule;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="timeblocks")
 */
class Timeblock
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $timeblock_id;
    
    /**
     * @ORM\Column(type="date")
     */    
    protected $timeblock_date;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timeblock_starttime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timeblock_endtime;
   
    /**
     * @ORM\ManyToOne(targetEntity="Schedule", inversedBy="timeblocks")
     * @ORM\JoinColumn(name="schedule_id", referencedColumnName="schedule_id")
     */
    protected $schedule_id;

    /**
     * Get timeblock_id
     *
     * @return integer 
     */
    public function getTimeblockId()
    {
        return $this->timeblock_id;
    }

    /**
     * Set timeblock_date
     *
     * @param \DateTime $timeblockDate
     * @return Timeblock
     */
    public function setTimeblockDate($timeblockDate)
    {
        $this->timeblock_date = $timeblockDate;

        return $this;
    }

    /**
     * Get timeblock_date
     *
     * @return \DateTime 
     */
    public function getTimeblockDate()
    {
        return $this->timeblock_date;
    }

    /**
     * Set timeblock_starttime
     *
     * @param \DateTime $timeblockStarttime
     * @return Timeblock
     */
    public function setTimeblockStarttime($timeblockStarttime)
    {
        $this->timeblock_starttime = $timeblockStarttime;

        return $this;
    }

    /**
     * Get timeblock_starttime
     *
     * @return \DateTime 
     */
    public function getTimeblockStarttime()
    {
        return $this->timeblock_starttime;
    }

    /**
     * Set timeblock_endtime
     *
     * @param \DateTime $timeblockEndtime
     * @return Timeblock
     */
    public function setTimeblockEndtime($timeblockEndtime)
    {
        $this->timeblock_endtime = $timeblockEndtime;

        return $this;
    }

    /**
     * Get timeblock_endtime
     *
     * @return \DateTime 
     */
    public function getTimeblockEndtime()
    {
        return $this->timeblock_endtime;
    }

    /**
     * Set schedule_id
     *
     * @param \MManager\MControlBundle\Entity\Schedule $scheduleId
     * @return Timeblock
     */
    public function setScheduleId(\MManager\MControlBundle\Entity\Schedule $scheduleId = null)
    {
        $this->schedule_id = $scheduleId;

        return $this;
    }

    /**
     * Get schedule_id
     *
     * @return \MManager\MControlBundle\Entity\Schedule 
     */
    public function getScheduleId()
    {
        return $this->schedule_id;
    }
}

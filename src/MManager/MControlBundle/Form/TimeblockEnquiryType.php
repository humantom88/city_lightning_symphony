<?php

namespace MManager\MControlBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TimeblockEnquiryType extends AbstractType
{
    private $schedules ;
    public function __construct($schedule = "")
    {
        if ($schedule != "") {
            $this->schedules = $schedule;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('timeblock_date', 'date');
        $builder->add('timeblock_starttime', 'time');
        $builder->add('timeblock_endtime', 'time');
        $builder->add('schedule', 'choice', array('choices' => array ($this->schedules->getScheduleId() => $this->schedules->getScheduleName())));
    }

    public function getName()
    {
        return 'timeblock_id';
    }
}
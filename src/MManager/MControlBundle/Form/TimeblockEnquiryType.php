<?php

namespace MManager\MControlBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TimeblockEnquiryType extends AbstractType
{
    private $schedules ;
    public function __construct($schedules = "")
    {
        if ($schedules != "") {
            foreach ($schedules as $schedule) {
                array_push($this->schedules,array ($schedule->getScheduleId() => $schedule->getScheduleName()));
            }
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('timeblock_date', 'date');
        $builder->add('timeblock_starttime', 'datetime');
        $builder->add('timeblock_endtime', 'datetime');
        $builder->add('schedule', 'choice', array('choices' => $this->schedules));
    }

    public function getName()
    {
        return 'timeblock_id';
    }
}
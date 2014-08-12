<?php

namespace MManager\MControlBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModemEnquiryType extends AbstractType
{    
    private $modemgroups = [];
    private $schedules = [];
    public function __construct($modemgroups = "", $schedules = "")
    {
        if ($modemgroups != "") {
            foreach ($modemgroups as $modemgroup) {
                array_push($this->modemgroups,array ($modemgroup->getModemgroupId() => $modemgroup->getModemgroupName()));
            }
        }
        
        if ($schedules != "") {
            foreach ($schedules as $schedule) {
                array_push($this->schedules, array($schedule->getScheduleId() => $schedule->getScheduleName()));
            }
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('modem_phone');
        $builder->add('modem_location', 'textarea');
        $builder->add('modem_serial');
        $builder->add('modem_group', 'choice', array('choices' => $this->modemgroups));
        $builder->add('schedule', 'choice', array('choices' => $this->schedules));
    }

    public function getName()
    {
        return 'modem_serial';
    }
}


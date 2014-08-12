<?php

namespace MManager\MControlBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ScheduleEnquiryType extends AbstractType
{
    private $modems = [];
    public function __construct($modems = "")
    {
        if ($modems != "") {
            foreach ($modems as $modem) {
                array_push($this->modems,array ($modem->getModemId() => $modem->getModemPhone()));
            }
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('schedule_name');
        //$builder->add('modem', 'choice', array('choices' => $this->modems));
    }

    public function getName()
    {
        return 'modem_serial';
    }
}


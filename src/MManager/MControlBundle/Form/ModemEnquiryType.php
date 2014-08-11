<?php

namespace MManager\MControlBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManager;

class ModemEnquiryType extends AbstractType
{    
    private $modemgroups = [];
    public function __construct($modemgroups = "")
    {
        if ($modemgroups != "") {
            foreach ($modemgroups as $modemgroup) {
                array_push($this->modemgroups,array ($modemgroup->getModemgroupId() => $modemgroup->getModemgroupName()));
            }
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('modem_phone');
        $builder->add('modem_location', 'textarea');
        $builder->add('modem_serial');
        $builder->add('modem_group', 'choice', array('choices' => $this->modemgroups));
    }

    public function getName()
    {
        return 'modem_serial';
    }
}


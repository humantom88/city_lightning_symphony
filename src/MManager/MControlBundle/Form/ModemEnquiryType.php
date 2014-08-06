<?php

namespace MManager\MControlBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModemEnquiryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('modem_phone');
        $builder->add('modem_location', 'textarea');
        $builder->add('modem_serial');
    }

    public function getName()
    {
        return 'modem_serial';
    }
}


<?php

namespace MManager\MControlBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModemEnquiryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('modemphone');
        $builder->add('modemlocation', 'textarea');
        $builder->add('modemserial');
    }

    public function getName()
    {
        return 'modemserial';
    }
}


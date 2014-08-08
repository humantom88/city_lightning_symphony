<?php

namespace MManager\MControlBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ModemGroupEnquiryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('modemgroup_name');
    }

    public function getName()
    {
        return 'modemgroup_name';
    }
}


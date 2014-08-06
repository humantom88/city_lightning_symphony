<?php

namespace MManager\MControlBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MManager\MControlBundle\Entity\Modem;

class ModemFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $modem1 = new Modem();
        $modem1->setSerial('A day with Symfony2');
        $modem1->setLocation('Lorem ipsum dolor sit amet, consectetur adipiscing eletra electrify denim vel ports.\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut velocity magna. Etiam vehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras el mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis, justo mauris lacinia libero, non facilisis purus ipsum non mi. Aliquam sollicitudin, augue id vestibulum iaculis, sem lectus convallis nunc, vel scelerisque lorem tortor ac nunc. Donec pharetra eleifend enim vel porta.');
        $modem1->setPhone('beach.jpg');
        $manager->persist($modem1);

        $modem2 = new Modem();
        $modem2->setSerial('The pool on the roof must have a leak');
        $modem2->setLocation('Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Na. Cras elementum molestie vestibulum. Morbi id quam nisl. Praesent hendrerit, orci sed elementum lobortis.');
        $modem2->setPhone('pool_leak.jpg');
        $manager->persist($modem2);

        $modem3 = new Modem();
        $modem3->setSerial('Misdirection. What the eyes see and the ears hear, the mind believes');
        $modem3->setLocation('Lorem ipsumvehicula nunc non leo hendrerit commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque.');
        $modem3->setPhone('misdirection.jpg');
        $manager->persist($modem3);

        $modem4 = new Modem();
        $modem4->setSerial('The grid - A digital frontier');
        $modem4->setLocation('Lorem commodo. Vestibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque. Nulla consectetur tempus nisl vitae viverra.');
        $modem4->setPhone('the_grid.jpg');
        $manager->persist($modem4);

        $modem5 = new Modem();
        $modem5->setSerial('You\'re either a one or a zero. Alive or dead');
        $modem5->setLocation('Lorem ipsum dolor sit amet, consectetur adipiscing elittibulum vulputate mauris eget erat congue dapibus imperdiet justo scelerisque.');
        $modem5->setPhone('one_or_zero.jpg');
        $manager->persist($modem5);

        $manager->flush();
    }

}
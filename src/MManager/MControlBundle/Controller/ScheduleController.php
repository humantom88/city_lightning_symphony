<?php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MManager\MControlBundle\Entity\Schedule;
use MManager\MControlBundle\Entity\ScheduleEnquiry;
use MManager\MControlBundle\Entity\Timeblock;
use MManager\MControlBundle\Entity\TimeblockEnquiry;
use MManager\MControlBundle\Form\ScheduleEnquiryType;
use MManager\MControlBundle\Form\TimeblockEnquiryType;
/**
 * Blog controller.
 */
class ScheduleController extends Controller
{
    /**
     * Show a blog entry
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $enquiry = new TimeblockEnquiry();
        $schedule = $em->getRepository('MManagerMControlBundle:Schedule')->find($id);
        $form = $this->createForm(new TimeblockEnquiryType($schedule), $enquiry);
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
                $newTimeblock = new Timeblock();
                $data = $form->getData();
                $timeblock_date = strtotime($data->getTimeblockDate()->format('Y-m-d H:i:s'));
                $timeblock_starttime = date_create($timeblock_date + strtotime($data->getTimeblockStarttime()->format('Y-m-d H:i:s')));
                print_r($timeblock_starttime);
                $newTimeblock->setTimeblockDate($data->getTimeblockDate());
                $newTimeblock->setTimeblockStarttime($data->getTimeblockStarttime());
                $newTimeblock->setTimeblockEndtime($data->getTimeblockEndtime());
                $newTimeblock->setScheduleId($schedule = $em->getRepository('MManagerMControlBundle:Schedule')->find($data->getScheduleId()));
                $em->persist($newTimeblock);
                $em->flush();
            }
        }

        $timeblocks = $em->getRepository('MManagerMControlBundle:Timeblock')->findBy(['schedule_id' => $id]);

        if (!$schedule) {
            throw $this->createNotFoundException('Unable to find schedule.');
        }

        return $this->render('MManagerMControlBundle:Schedule:show.html.twig', array(
            'schedule'      => $schedule,
            'form'          => $form->createView(),
            'timeblocks'    => $timeblocks
        ));
    }
    
    public function showAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $schedules = $em->getRepository('MManagerMControlBundle:Schedule')->findAll();
        $modems = $em->getRepository('MManagerMControlBundle:Modem')->findAll();
        
        $enquiry = new ScheduleEnquiry();
        $form = $this->createForm(new ScheduleEnquiryType($modems), $enquiry);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                
                $newschedule = new Schedule();
                $data = $form->getData();
                $newschedule->setScheduleName($data->getScheduleName());
                
                $em->persist($newschedule);
                $em->flush();

                return $this->redirect($this->generateUrl('MManagerMControlBundle_schedule_showAll'));
            }
        }        
        //if (!$modems) {
        //    throw $this->createNotFoundException('There are no modem registered in Database.');
        //}
        return $this->render('MManagerMControlBundle:Schedule:showall.html.twig', array(
            'schedules' => $schedules,
            'modems' => $modems,
            'form' => $form->createView()
        ));        
    }

    
    public function deleteAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $schedules = $em->getRepository('MManagerMControlBundle:Schedule')->findBy(array ('schedule_id' => $request->request->get('ids')));
        
        if (is_array($schedules)) {
            foreach ($schedules as $schedule) {
                $em->remove($schedule);
                $em->flush();
            }
        } else if ($schedules != "") {
            $em->remove($schedules);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('MManagerMControlBundle_schedule_showAll'));
    }
    
    public function deleteTimeblockAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $timeblocks = $em->getRepository('MManagerMControlBundle:Timeblock')->findBy(array ('timeblock_id' => $request->request->get('ids')));
        
        if (is_array($timeblocks)) {
            foreach ($timeblocks as $timeblock) {
                $em->remove($timeblock);
                $em->flush();
            }
        } else if ($timeblocks != "") {
            $em->remove($timeblock);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('MManagerMControlBundle_schedule_showAll'));
    }
    
//    public function getModemAsArray() 
//    {
//        $request = $this->getRequest();
//        $em = $this->getDoctrine()->getManager();
//        $modems = $em->getRepository('MManagerMControlBundle:Modem')->findBy(array ('modem_id' => $request->request->get('ids')));
//        if (is_array($modems)) {
//            foreach ($modems as $modem) {
//                if (is_object($modem)) {
//                    $result = [
//                        'modem_id' => $modem->getModemId(),
//                        'modem_location' => $modem->getModemLocation(),
//                        'modem_phone' => $modem->getModemPhone()
//                    ];
//                } else {
//                    $result = false;
//                }
//            }
//        } else {
//            if (is_object($modem)) {
//                $result = [
//                    'modem_id' => $modem->getModemId(),
//                    'modem_location' => $modem->getModemLocation(),
//                    'modem_phone' => $modem->getModemPhone()
//                ];
//            } else {
//                $result = false;
//            }
//        }
//        return $result;
//    }
}
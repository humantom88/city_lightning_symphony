<?php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MManager\MControlBundle\Entity\Schedule;
use MManager\MControlBundle\Entity\ScheduleEnquiry;
use MManager\MControlBundle\Entity\Timeblock;
use MManager\MControlBundle\Entity\TimeblockEnquiry;
use MManager\MControlBundle\Form\ScheduleEnquiryType;
use MManager\MControlBundle\Form\TimeblockEnquiryType;
use DateTime;
use DateTimeZone;

/**
 * Blog controller.
 */
class ScheduleController extends Controller
{
    /**
     * Show a schedule entry
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $enquiry = new TimeblockEnquiry();
        $schedule = $em->getRepository('MManagerMControlBundle:Schedule')->find($id);
        $form = $this->createForm(new TimeblockEnquiryType($schedule), $enquiry);
        $timeblocks = $em->getRepository('MManagerMControlBundle:Timeblock')->findBy(['schedule_id' => $id]);
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            
            if ($form->isValid()) {
                $newTimeblock = new Timeblock();
                $data = $form->getData();
                $timeblock_date = strtotime($data->getTimeblockDate()->format('Y-m-d H:i:s'));
                $newTimeblock->setTimeblockDate($data->getTimeblockDate());

                $newDateTime = new DateTime();
                $timeblock_starttime = $timeblock_date + strtotime($data->getTimeblockStarttime()->format('Y-m-d H:i'));
                $newDateTime->setTimestamp($timeblock_starttime);
                $newTimeblock->setTimeblockStarttime($newDateTime->add(date_interval_create_from_date_string('3 hours')));
                $newDateTime = new DateTime();
                $timeblock_endtime = $timeblock_date + strtotime($data->getTimeblockEndtime()->format('Y-m-d H:i'));
                $newDateTime->setTimestamp($timeblock_endtime);
                $newTimeblock->setTimeblockEndtime($newDateTime->add(date_interval_create_from_date_string('3 hours')));

                $newTimeblock->setScheduleId($schedule = $em->getRepository('MManagerMControlBundle:Schedule')->find($data->getScheduleId()));
                $em->persist($newTimeblock);
                $em->flush();

                $timeblocks = $em->getRepository('MManagerMControlBundle:Timeblock')->findBy(['schedule_id' => $id]);
                
                return $this->redirect($this->generateUrl('MManagerMControlBundle_schedule_show', array('id' => $id)));
            }
        }       

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

}
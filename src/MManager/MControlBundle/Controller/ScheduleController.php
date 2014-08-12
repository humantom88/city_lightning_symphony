<?php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MManager\MControlBundle\Classes\Gammu;
use MManager\MControlBundle\Entity\Schedule;
use MManager\MControlBundle\Entity\ScheduleEnquiry;
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

        $schedule = $em->getRepository('MManagerMControlBundle:Schedule')->find($id);
        $timeblocks = $em->getRepository('MManagerMControlBundle:Timeblock')->findBy(['schedule_id' => $id]);
        $enquiry = new TimeblockEnquiry();
        $form = $this->createForm(new TimeblockEnquiryType($schedule), $enquiry);
        
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

    
    public function deleteModemAction()
    {
        $modems = $this->getModemAsArray();
        $em = $this->getDoctrine()->getManager();
        if (is_array($modems)) {
            foreach ($modems as $modem) {
                $em->remove($modem);
                $em->flush();
            }
        } else if ($modems != "") {
            $em->remove($modems);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('MManagerMControlBundle_modem_showAll'));
    }
    
      
    public function getModemAsArray() 
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $modems = $em->getRepository('MManagerMControlBundle:Modem')->findBy(array ('modem_id' => $request->request->get('ids')));
        if (is_array($modems)) {
            foreach ($modems as $modem) {
                if (is_object($modem)) {
                    $result = [
                        'modem_id' => $modem->getModemId(),
                        'modem_location' => $modem->getModemLocation(),
                        'modem_phone' => $modem->getModemPhone()
                    ];
                } else {
                    $result = false;
                }
            }
        } else {
            if (is_object($modem)) {
                $result = [
                    'modem_id' => $modem->getModemId(),
                    'modem_location' => $modem->getModemLocation(),
                    'modem_phone' => $modem->getModemPhone()
                ];
            } else {
                $result = false;
            }
        }
        return $result;
    }
}
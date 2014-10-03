<?php
// src/Blogger/BlogBundle/Controller/BlogController.php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use MManager\MControlBundle\Classes\Gammu;
use MManager\MControlBundle\Entity\Modem;
use MManager\MControlBundle\Entity\ModemEnquiry;
use MManager\MControlBundle\Form\ModemEnquiryType;
/**
 * Blog controller.
 */
class ModemController extends Controller
{
    /**
     * Show a blog entry
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $modem = $em->getRepository('MManagerMControlBundle:Modem')->find($id);
        $modemgroups = $em->getRepository('MManagerMControlBundle:ModemGroup')->findAll();
        $schedules = $em->getRepository('MManagerMControlBundle:Schedule')->findAll();
        $messages = $em->getRepository('MManagerMControlBundle:SmsMessage')->findBy(array('modem_id' => $id));
        $enquiry = new ModemEnquiry();
        $form = $this->createForm(new ModemEnquiryType($modemgroups, $schedules), $enquiry);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $data = $form->getData();
                if ($data->getModemSerial()) {
                    $modem->setModemSerial($data->getModemSerial());
                }
                if ($data->getModemPhone()) {
                    $modem->setModemPhone($data->getModemPhone());
                }
                if ($data->getModemLocation()) {
                    $modem->setModemLocation($data->getModemLocation());
                }
                if ($data->getModemSchedule()) {
                    $modem->setScheduleId($em->getRepository('MManagerMControlBundle:Schedule')->find($data->getModemSchedule()));
                }
                if ($data->getModemGroup()) {
                    $modem->setModemGroupId($em->getRepository('MManagerMControlBundle:ModemGroup')->find($data->getModemGroup()));
                }
                
                $em->flush();
            }
        }
        
        if (!$modem) {
            throw $this->createNotFoundException('Unable to find modem.');
        }

        return $this->render('MManagerMControlBundle:Modem:show.html.twig', array(
            'modem'      => $modem,
            'messages'   => $messages,
            'form'       => $form->createView()
        ));
    }
        
    public function showAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $modems = $em->getRepository('MManagerMControlBundle:Modem')->findAll();
        $modemgroups = $em->getRepository('MManagerMControlBundle:ModemGroup')->findAll();
        $schedules = $em->getRepository('MManagerMControlBundle:Schedule')->findAll();
        
        $enquiry = new ModemEnquiry();
        $form = $this->createForm(new ModemEnquiryType($modemgroups, $schedules), $enquiry);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                
                $newmodem= new Modem();
                $data = $form->getData();
                $newmodem->setModemLocation($data->getModemLocation());
                $newmodem->setModemPhone($data->getModemPhone());
                $newmodem->setModemSerial($data->getModemSerial());
                
                $em->persist($newmodem);
                $em->flush();

                return $this->redirect($this->generateUrl('MManagerMControlBundle_modem_showAll'));
            }
        }        
        return $this->render('MManagerMControlBundle:Modem:showall.html.twig', array(
            'modems' => $modems,
            'modemgroups' => $modemgroups,
            'schedules' => $schedules,
            'form' => $form->createView()
        ));        
    }

    public function sendSMSAction()
    {   //При развертывании на изменить директории
        $gammu = new Gammu([
            'inbox' => 'c:/gammu/inbox/',
            'outbox' => 'c:/gammu/outbox/',
            'sent' => 'c:/gammu/sent/',
            'errors' => 'c:/gammu/error/'
        ]);
        
        $modems = $this->getModemAsArray();
        
        if (!$modems['modem_phone']) {
            foreach ($modems as $modem) {
                $gammu->sendSMS($modem['modem_phone'], '5492 out3 pulse2');
                //file_put_contents('c:/log/log.txt', date('Y-m-d-h-m-s') . ' Message ' . '"5492 out3 pulse2"' . ' was sent to '. $modem['modem_phone'] . '.' . chr(13) , FILE_APPEND);
            }
        } else {
            $gammu->sendSMS($modems['modem_phone'], '5492 out3 pulse2');
            //file_put_contents('c:/log/log.txt', date('Y-m-d-h-m-s') . ' Message ' . '"5492 out3 pulse2"' . ' was sent to '. $modems['modem_phone'] . '.' . chr(13) , FILE_APPEND);
        }
        return $this->showAllAction();
    }
    
    public function deleteModemAction()
    {
        $modems = $this->getModemAsObject();
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
    
    public function getModemAsObject() 
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $modems = $em->getRepository('MManagerMControlBundle:Modem')->findBy(array ('modem_id' => $request->request->get('ids')));

        return $modems;
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
    
    public function getModemGroupById ($modemGroupId)
    {
        return $this->getDoctrine()->getRepository('MManagerMControlBundle:ModemGroup')->find($modemGroupId);
    }
       
    public function updateModemStatusAction()
    {
        $request = $this->getRequest();        
        $data = $request->get('data');

        $em = $this->getDoctrine()->getManager();
        $modems = $em->getRepository('MManagerMControlBundle:Modem')->findBy(array ('modem_id' => $data));
        
        $response = [];
        
        if ($modems) {
            foreach ($modems as $modem) {
                array_push($response, [
                    'id' => $modem->getModemId(),
                    'status' => $modem->getModemStatus()
                ]);
            }
        } else {
            $response = "";
        }

        return new Response(json_encode($response)); 
    }
}
<?php
// src/Blogger/BlogBundle/Controller/BlogController.php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        if (!$modem) {
            throw $this->createNotFoundException('Unable to find modem.');
        }

        return $this->render('MManagerMControlBundle:Modem:show.html.twig', array(
            'modem'      => $modem,
        ));
    }
    
    public function showAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $modems = $em->getRepository('MManagerMControlBundle:Modem')->findAll();
        $modemgroups = $em->getRepository('MManagerMControlBundle:ModemGroup')->findAll();
        
        $enquiry = new ModemEnquiry();
        $form = $this->createForm(new ModemEnquiryType($modemgroups), $enquiry);
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
        if (!$modems) {
            throw $this->createNotFoundException('There are no modem registered in Database.');
        }
        
        return $this->render('MManagerMControlBundle:Modem:showall.html.twig', array(
            'modems' => $modems,
            'modemgroups' => $modemgroups,
            'form' => $form->createView()
        ));        
    }
    
    public function createAction()
    {
        $entity = new Modem();
        $request = $this->getRequest();
        $form = $this->createForm(new EntityType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Нужно указать родительский объект
            foreach ($entity->getNewsLinks() as $link)
            {
                $link->setNews($entity);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('news_show', array('id' => $entity->getId())));
        }

        return array(
           'entity' => $entity,
           'form' => $form->createView());
    }
    
    public function sendSMSAction()
    {
        $gammu = new Gammu([
            'inbox' => 'c:/gammu/inbox/',
            'outbox' => 'c:/gammu/outbox/',
            'sent' => 'c:/gammu/sent/',
            'errors' => 'c:/gammu/errors/'
        ]);
        
        $modems = $this->getModemAsArray();
        
        foreach ($modems as $modem) {
            $gammu->sendSMS($modem['modem_phone'], '5492 out3 pulse2');
            file_put_contents('c:/log/log.txt', date('Y-m-d-h-m-s') . ' Message ' . '"5492 out3 pulse2"' . ' was sent to '. $modem['modem_phone'] . '.' . chr(13) , FILE_APPEND);
        }
        return $this->showAllAction();
    }
    
    public function getModemAsArray() 
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $modems = $em->getRepository('MManagerMControlBundle:Modem')->findBy(array ('modem_id' => $request->request->get('ids')));

        return $modems;
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
}
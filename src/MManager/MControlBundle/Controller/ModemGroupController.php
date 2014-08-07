<?php
// src/Blogger/BlogBundle/Controller/BlogController.php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
 * Blog controller.
 */
class ModemGroupController extends Controller
{
    /**
     * Show a blog entry
     */
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $modemgroup = $em->getRepository('MManagerMControlBundle:ModemGroup')->find($id);

        if (!$modemgroup) {
            throw $this->createNotFoundException('Unable to find modem group.');
        }

        return $this->render('MManagerMControlBundle:ModemGroup:show.html.twig', array(
            'modemgroup'      => $modemgroup
        ));
    }
    
    public function showAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $modemgroups = $em->getRepository('MManagerMControlBundle:ModemGroup')->findAll();
        
        if (!$modemgroups) {
            throw $this->createNotFoundException('There are no modem registered in Database.');
        }
        
        return $this->render('MManagerMControlBundle:ModemGroup:showall.html.twig', array(
            'modemgroups' => $modemgroups,
        ));
    }
    
//    public function createAction()
//    {
//        $entity = new Modem();
//        $request = $this->getRequest();
//        $form = $this->createForm(new EntityType(), $entity);
//        $form->bindRequest($request);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//
//            // Нужно указать родительский объект
//            foreach ($entity->getNewsLinks() as $link)
//            {
//                $link->setNews($entity);
//            }
//            $em->persist($entity);
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('news_show', array('id' => $entity->getId())));
//        }
//
//        return array(
//           'entity' => $entity,
//           'form' => $form->createView());
//    }
    
    public function sendSMSAction()
    {
        $gammu = new Gammu([
            'inbox' => 'c:/gammu/inbox/',
            'outbox' => 'c:/gammu/outbox/',
            'sent' => 'c:/gammu/sent/',
            'errors' => 'c:/gammu/errors/'
        ]);
        $request = $this->getRequest();
        $modemObjects = $request->request->get('ids');
        $em = $this->getDoctrine()->getManager();
        $modems = $em->getRepository('MManagerMControlBundle:Modem')->findBy(array ('modem_id' => $request->request->get('ids')));
        foreach ($modems as $modem) {
            $m = $modem->getModemAsArray();
            $gammu->sendSMS($m['modem_phone'], '5492 out3 pulse2');
            file_put_contents('c:/log/log.txt', date('Y-m-d-h-m-s') . ' Message ' . '"5492 out3 pulse2"' . ' was sent to '. $m['modem_phone'] . '.' . chr(13) , FILE_APPEND);
        }
        return $this->showAllAction();
    }
}
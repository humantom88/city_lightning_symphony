<?php
// src/Blogger/BlogBundle/Controller/BlogController.php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MManager\MControlBundle\Classes\Gammu;
use MManager\MControlBundle\Entity\ModemGroup;
use MManager\MControlBundle\Entity\ModemGroupEnquiry;
use MManager\MControlBundle\Form\ModemGroupEnquiryType;
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
            'modemgroup'      => $modemgroup,
            'id'              => $id
        ));
    }
    
    public function showAllAction()
    {
        $enquiry = new ModemGroupEnquiry();
        $form = $this->createForm(new ModemGroupEnquiryType(), $enquiry);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $newmodemgroup= new ModemGroup();
                $data = $form->getData();
                $newmodemgroup->setModemGroupName($data->getModemGroupName());

                $em->persist($newmodemgroup);
                $em->flush();
                
                return $this->redirect($this->generateUrl('MManagerMControlBundle_modemgroup_showAll'));
            }
        }
        $em = $this->getDoctrine()->getManager();
        
        $modemgroups = $em->getRepository('MManagerMControlBundle:ModemGroup')->findAll();
        
        //if (!$modemgroups) {
        //    throw $this->createNotFoundException('There are no modem registered in Database.');
        //}
        
        return $this->render('MManagerMControlBundle:ModemGroup:showall.html.twig', array(
            'modemgroups' => $modemgroups,
            'form' => $form->createView(),
        ));        
    }
    
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
    
    public function deleteModemGroupAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $modemgroups = $em->getRepository('MManagerMControlBundle:ModemGroup')->findBy(array ('modemgroup_id' => $request->request->get('ids')));

        if (is_array($modemgroups)) {
            foreach ($modemgroups as $modemgroup) {
                $em->remove($modemgroup);
                $em->flush();
            }
        } else if ($modemgroups != "") {
            $em->remove($modemgroups);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('MManagerMControlBundle_modemgroup_showAll'));
    }
}
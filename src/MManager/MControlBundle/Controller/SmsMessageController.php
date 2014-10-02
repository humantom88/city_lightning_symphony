<?php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MManager\MControlBundle\Classes\Gammu;
use MManager\MControlBundle\Entity\Modem;
use MManager\MControlBundle\Entity\ModemEnquiry;
use MManager\MControlBundle\Form\ModemEnquiryType;
/**
 * Blog controller.
 */
class SmsMessageController extends Controller
{
    /**
     * Show a blog entry
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $smsmessage = $em->getRepository('MManagerMControlBundle:SmsMessage')->find($id);

        if (!$smsmessage) {
            throw $this->createNotFoundException('Unable to find smsmessages.');
        }

        return $this->render('MManagerMControlBundle:SmsMessage:show.html.twig', array(
            'smsmessage'      => $smsmessage,
        ));
    }
    
    public function showAllAction()
    {
        $em = $this->getDoctrine()->getManager();
        $smsmessages = $em->getRepository('MManagerMControlBundle:SmsMessage')->findAll();
        
        //$enquiry = new ModemEnquiry();
        //$form = $this->createForm(new ModemEnquiryType($modemgroups), $enquiry);
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                return $this->redirect($this->generateUrl('MManagerMControlBundle_smsmessages_showAll'));
            }
        }
        //if (!$smsmessages) {
        //    throw $this->createNotFoundException('There are no sms smsmessages in Database.');
        //}
        
        return $this->render('MManagerMControlBundle:SmsMessage:showall.html.twig', array(
            'smsmessages' => $smsmessages
        ));        
    }
    public function deleteAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $smsmessages = $em->getRepository('MManagerMControlBundle:SmsMessage')->findBy(array ('sms_id' => $request->request->get('ids')));
        
        if (is_array($smsmessages)) {
            foreach ($smsmessages as $smsmessage) {
                $em->remove($smsmessage);
                $em->flush();
            }
        } else if ($smsmessage != "") {
            $em->remove($smsmessage);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('MManagerMControlBundle_smsmessage_showAll'));
    }
}
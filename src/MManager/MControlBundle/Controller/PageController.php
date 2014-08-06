<?php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MManager\MControlBundle\Entity\ModemEnquiry;
use MManager\MControlBundle\Form\ModemEnquiryType;

 class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('MManagerMControlBundle:Page:index.html.twig');
    }
    
    public function modemFormViewAction()
    {
        $enquiry = new ModemEnquiry();
        $form = $this->createForm(new ModemEnquiryType(), $enquiry);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact enquiry from symblog')
                    ->setFrom('enquiries@symblog.co.uk')
                    ->setTo($this->container->getParameter('mmanager_mcontrol.emails.contact_email'))
                    ->setBody($this->renderView('MManagerMControlBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));
                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('mmanager-notice', 'Your contact enquiry was successfully sent. Thank you!');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('MManagerMControlBundle_modemview'));
            }
        }

        return $this->render('MManagerMControlBundle:Page:modemformview.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


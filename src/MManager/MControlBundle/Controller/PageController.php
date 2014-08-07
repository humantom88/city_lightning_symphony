<?php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MManager\MControlBundle\Entity\Modem;
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
            $form->bind($request);

            if ($form->isValid()) {
                
                $em = $this->getDoctrine()->getEntityManager();
                $newmodem= new Modem();
                $newmodem->setModemLocation('Vohtoga');
                $newmodem->setModemPhone('89114425750');
                $newmodem->setModemSerial('AF89014232');
                $em->persist($newmodem);
                $em->flush();
                
                //$message = \Swift_Message::newInstance()
                //    ->setSubject('Contact enquiry from symblog')
                //    ->setFrom('enquiries@symblog.co.uk')
                //    ->setTo($this->container->getParameter('mmanager_mcontrol.emails.contact_email'))
                //    ->setBody($this->renderView('MManagerMControlBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));
                //$this->get('mailer')->send($message);

                //$this->get('session')->setFlash('mmanager-notice', 'Your contact enquiry was successfully sent. Thank you!');

                // Redirect - This is important to prevent users re-posting
                // the form if they refresh the page
                return $this->redirect($this->generateUrl('MManagerMControlBundle_modem_showAll'));
            }
        }

        return $this->render('MManagerMControlBundle:Page:modemformview.html.twig', array(
            'form' => $form->createView()
        ));
    }
}

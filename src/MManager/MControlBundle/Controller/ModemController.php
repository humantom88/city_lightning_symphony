<?php
// src/Blogger/BlogBundle/Controller/BlogController.php

namespace MManager\MControlBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        
        if (!$modems) {
            throw $this->createNotFoundException('There are no modem registered in Database.');
        }
        
        return $this->render('MManagerMControlBundle:Modem:showall.html.twig', array(
            'modems' => $modems,
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
}
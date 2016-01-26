<?php

namespace DocBundle\Controller;

use DocBundle\Entity\Reseau;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $reseaus = $em->getRepository('DocBundle:Reseau')->findAll();
        return $this->render('DocBundle:Default:index.html.twig', array(
            'reseaus' => $reseaus,
            )
        );
    }
}

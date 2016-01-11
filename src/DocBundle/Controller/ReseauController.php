<?php

namespace DocBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use DocBundle\Entity\Reseau;

/**
 * Reseau controller.
 *
 */
class ReseauController extends Controller
{
    /**
     * Lists all Reseau entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $reseaus = $em->getRepository('DocBundle:Reseau')->findAll();

        return $this->render('DocBundle:reseau:index.html.twig', array(
            'reseaus' => $reseaus,
        ));
    }

    /**
     * Creates a new Reseau entity.
     *
     */
    public function newAction(Request $request)
    {
        $reseau = new Reseau();
        $form = $this->createForm('DocBundle\Form\ReseauType', $reseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reseau);
            $em->flush();

            return $this->redirectToRoute('reseau_show', array('id' => $reseau->getId()));
        }

        return $this->render('DocBundle:reseau:new.html.twig', array(
            'reseau' => $reseau,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Reseau entity.
     *
     */
    public function showAction(Reseau $reseau)
    {
        $deleteForm = $this->createDeleteForm($reseau);

        return $this->render('DocBundle:reseau:show.html.twig', array(
            'reseau' => $reseau,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Reseau entity.
     *
     */
    public function editAction(Request $request, Reseau $reseau)
    {
        $deleteForm = $this->createDeleteForm($reseau);
        $editForm = $this->createForm('DocBundle\Form\ReseauType', $reseau);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reseau);
            $em->flush();

            return $this->render('DocBundle:reseau:edit.html.twig', array(
                'reseau' => $reseau->getId(),
                'confirmation' => true,
                'form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                ));
        }

        return $this->render('DocBundle:reseau:edit.html.twig', array(
            'reseau' => $reseau,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Reseau entity.
     *
     */
    public function deleteAction(Request $request, Reseau $reseau)
    {
        $em = $this->getDoctrine()->getManager();
        if ($reseau == null) {
            throw $this->createNotFoundException("Le réseau  ".$reseau.getCode()." n'existe pas.");
        }
        if ($request->isMethod('GET')) {
            $em->remove($reseau);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Réseau bien supprimée.');
            return $this->redirect($this->generateUrl('reseau_index'));
        }
        return $this->render('@Doc/reseau/show.html.twig', array(
            'reseau' => $reseau
        ));

        /*$form = $this->createDeleteForm($reseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reseau);
            $em->flush();
        }

        return $this->redirectToRoute('reseau_index');*/
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function menuAction() {
        $em = $this->getDoctrine()->getManager();

        $reseaus = $em->getRepository('DocBundle:Reseau')->findAll();

        return $this->render('DocBundle:reseau:menu.html.twig', array(
            'reseaux' => $reseaus,
        ));
    }

    /**
     * Finds and displays a Reseau parametrage list.
     *
     */
    public function showParametrageAction(Reseau $reseau)
    {
        $em = $this->getDoctrine()->getManager();
        $parametrage = $em->getRepository('DocBundle:Parametrage')->getParametrageWithReseau($reseau->getId());

        //$deleteForm = $this->createDeleteForm($reseau);

        return $this->render('DocBundle:reseau:reseau_params.html.twig', array(
            'parametrages' => $parametrage
            //'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to delete a Reseau entity.
     *
     * @param Reseau $reseau The Reseau entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Reseau $reseau)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reseau_delete', array('id' => $reseau->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

<?php

namespace DocBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use DocBundle\Entity\Reseau;
use DocBundle\Form\ReseauType;

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

            return $this->redirectToRoute('reseau_edit', array('id' => $reseau->getId()));
        }

        return $this->render('DocBundle:reseau:edit.html.twig', array(
            'reseau' => $reseau,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Reseau entity.
     *
     */
    public function deleteAction(Request $request, Reseau $reseau)
    {
        $form = $this->createDeleteForm($reseau);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reseau);
            $em->flush();
        }

        return $this->redirectToRoute('reseau_index');
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

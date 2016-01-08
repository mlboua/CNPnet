<?php

namespace DocBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;

use DocBundle\Entity\CnpDocument;
use DocBundle\Form\CnpDocumentType;

/**
 * CnpDocument controller.
 *
 */
class CnpDocumentController extends Controller
{
    /**
     * Lists all CnpDocument entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cnpDocuments = $em->getRepository('DocBundle:CnpDocument')->findAll();

        return $this->render('DocBundle:cnpdocument:index.html.twig', array(
            'cnpDocuments' => $cnpDocuments,
        ));
    }

    /**
     * Creates a new CnpDocument entity.
     *
     */
    public function newAction(Request $request)
    {
        $cnpDocument = new CnpDocument();
        $form = $this->createForm('DocBundle\Form\CnpDocumentType', $cnpDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /*$file = $cnpDocument->getPdfSource();
            $file->setTitle($this->generateFileName($cnpDocument));
            $cnpDocument->setPdfSource($file);*/

            $em = $this->getDoctrine()->getManager();
            $em->persist($cnpDocument);
            $em->flush();

            return $this->redirectToRoute('cnpdocument_show', array('id' => $cnpDocument->getId()));
        }

        return $this->render('DocBundle:cnpdocument:new.html.twig', array(
            'cnpDocument' => $cnpDocument,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CnpDocument entity.
     *
     */
    public function showAction(CnpDocument $cnpDocument)
    {
        $deleteForm = $this->createDeleteForm($cnpDocument);

        return $this->render('DocBundle:cnpdocument:show.html.twig', array(
            'cnpDocument' => $cnpDocument,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing CnpDocument entity.
     *
     */
    public function editAction(Request $request, CnpDocument $cnpDocument)
    {
        $deleteForm = $this->createDeleteForm($cnpDocument);
        $editForm = $this->createForm('DocBundle\Form\CnpDocumentType', $cnpDocument);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            /*$file = $cnpDocument->getPdfSource();
            $file->setTitle($this->generateFileName($cnpDocument));
            $cnpDocument->setPdfSource($file);*/
            $em = $this->getDoctrine()->getManager();
            $em->persist($cnpDocument);
            $em->flush();

            return $this->redirectToRoute('cnpdocument_edit', array('id' => $cnpDocument->getId()));
        }

        return $this->render('DocBundle:cnpdocument:edit.html.twig', array(
            'cnpDocument' => $cnpDocument,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a CnpDocument entity.
     *
     */
    public function deleteAction(Request $request, CnpDocument $cnpDocument)
    {
        $form = $this->createDeleteForm($cnpDocument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cnpDocument);
            $em->flush();
        }

        return $this->redirectToRoute('cnpdocument_index');
    }

    /**
     * Creates a form to delete a CnpDocument entity.
     *
     * @param CnpDocument $cnpDocument The CnpDocument entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CnpDocument $cnpDocument)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cnpdocument_delete', array('id' => $cnpDocument->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param CnpDocument $doc
     * @return string
     */
    private function generateFileName(CnpDocument $doc)
    {
        return $doc->getContrat().'_'.$doc->getType().'_'.$doc->getReference().'.pdf';
    }
}

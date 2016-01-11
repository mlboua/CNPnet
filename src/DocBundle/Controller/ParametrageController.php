<?php

namespace DocBundle\Controller;

use DocBundle\Entity\Reseau;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;

use DocBundle\Entity\Parametrage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Parametrage controller.
 *
 */
class ParametrageController extends Controller
{
    /**
     * Lists all Parametrage entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $parametrages = $em->getRepository('DocBundle:Parametrage')->findAll();

        return $this->render('DocBundle:parametrage:index.html.twig', array(
            'parametrages' => $parametrages,
        ));
    }

    /**
     * Creates a new Parametrage entity.
     *
     */
    public function newAction(Request $request)
    {
        $parametrage = new Parametrage();

        $form = $this->createForm('DocBundle\Form\ParametrageType', $parametrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $parametrage->getPdfSource()->setTitle($this->generateFileName($parametrage));
            $stream = fopen($parametrage->getPdfSource()->getFile(), 'rb');
            $parametrage->getPdfSource()->SetFile(stream_get_contents($stream));

            $em = $this->getDoctrine()->getManager();
            $em->persist($parametrage);
            $em->flush();

            return $this->redirectToRoute('parametrage_show', array('id' => $parametrage->getId()));
        }

        return $this->render('DocBundle:parametrage:new.html.twig', array(
            'parametrage' => $parametrage,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Parametrage entity.
     *
     */
    public function showAction(Parametrage $parametrage)
    {
        $deleteForm = $this->createDeleteForm($parametrage);

        return $this->render('DocBundle:parametrage:show.html.twig', array(
            'parametrage' => $parametrage,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a Parametrage associated pdf document.
     *
     */
    public function showPdfAction(Parametrage $parametrage)
    {
        $pdfFile = $parametrage->getPdfSource()->getFile();
        $response = new Response(stream_get_contents($pdfFile), 200, array('Content-Type' => 'application/pdf'));
        return $response;
    }

    /**
     * Displays a form to edit an existing Parametrage entity.
     *
     */
    public function editAction(Request $request, Parametrage $parametrage)
    {
        // TODO: Manage pdf data updating form
        //$temp_file = tmpfile();
       // file_put_contents($temp_file, $parametrage->getPdfSource());
        //$tempname = tempnam('', 'report_');
        //rename($tempname, $parametrage->getPdfName());
        //fopen($parametrage->getPdfName(), 'rb');
        //$metaDatas = stream_get_meta_data($temp_file);
        //$tmpFilename = $metaDatas['uri'];
        //$parametrage->setType($parametrage->getPdfName());
        //$parametrage->setPdfSource(new File($parametrage->getPdfName()));


        $deleteForm = $this->createDeleteForm($parametrage);
        $editForm = $this->createForm('DocBundle\Form\ParametrageType', $parametrage);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $parametrage->getPdfSource->setTitle($this->generateFileName($parametrage));
            if($parametrage->getPdfSource() !== null) {
                $stream = fopen($parametrage->getPdfSource(), 'rb');
                $parametrage->setPdfSource(stream_get_contents($stream));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($parametrage);
            $em->flush();

            return $this->render('DocBundle:parametrage:edit.html.twig', array(
                'parametrage' => $parametrage,
                'form' => $editForm->createView(),
                'confirmation' => true,
                'delete_form' => $deleteForm->createView(),
            ));
        }

        return $this->render('DocBundle:parametrage:edit.html.twig', array(
            'parametrage' => $parametrage,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Parametrage entity.
     *
     */
    public function deleteAction(Request $request, Parametrage $parametrage)
    {
        $em = $this->getDoctrine()->getManager();
        if ($parametrage == null) {
            throw $this->createNotFoundException("Le réseau  ".$parametrage.getId()." n'existe pas.");
        }
        if ($request->isMethod('GET')) {
            $em->remove($parametrage);
            $em->flush();
            $request->getSession()->getFlashBag()->add('info', 'Parametrage bien supprimée.');
            return $this->redirect($this->generateUrl('parametrage_index'));
        }
        return $this->render('DocBundle:parametrage:show.html.twig', array(
            'parametrage' => $parametrage
        ));

        /*$form = $this->createDeleteForm($parametrage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($parametrage);
            $em->flush();
        }

        return $this->redirectToRoute('parametrage_index');*/
    }

    /**
     * Creates a form to delete a Parametrage entity.
     *
     * @param Parametrage $parametrage The Parametrage entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Parametrage $parametrage)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parametrage_delete', array('id' => $parametrage->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param Parametrage $doc
     * @return string
     */
    private function generateFileName(Parametrage $doc)
    {
        return $doc->getContrat().'_'.$doc->getType().'_'.$doc->getReference().'.pdf';
    }
}

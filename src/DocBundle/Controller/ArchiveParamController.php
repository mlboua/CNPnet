<?php

namespace DocBundle\Controller;

use DocBundle\Entity\Pdf;
use DocBundle\Entity\Version;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use DocBundle\Entity\ArchiveParam;
use DocBundle\Form\ArchiveParamType;

/**
 * ArchiveParam controller.
 *
 */
class ArchiveParamController extends Controller
{
    /**
     * Lists all ArchiveParam entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $archiveParams = $em->getRepository('DocBundle:ArchiveParam')->findAll();

        return $this->render('@Doc/archiveparam/index.html.twig', array(
            'archiveParams' => $archiveParams,
        ));
    }

    /**
     * Creates a new ArchiveParam entity.
     *
     */
    public function newAction(Request $request)
    {
        $archiveParam = new ArchiveParam();
        $form = $this->createForm('DocBundle\Form\ArchiveParamType', $archiveParam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($archiveParam);
            $em->flush();

            return $this->redirectToRoute('archiveparam_show', array('id' => $archiveParam->getId()));
        }

        return $this->render('@Doc/archiveparam/new.html.twig', array(
            'archiveParam' => $archiveParam,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ArchiveParam entity.
     *
     */
    public function showAction(ArchiveParam $archiveParam)
    {
        $deleteForm = $this->createDeleteForm($archiveParam);

        return $this->render('@Doc/archiveparam/show.html.twig', array(
            'archiveParam' => $archiveParam,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ArchiveParam entity.
     *
     */
    public function editAction(Request $request, ArchiveParam $archiveParam)
    {
        $deleteForm = $this->createDeleteForm($archiveParam);
        $editForm = $this->createForm('DocBundle\Form\ArchiveParamType', $archiveParam);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $newArchive = $archiveParam->hydrateNewArchive();
            if($archiveParam->getCurrentPdf()->getFile() !== null) {
                $pdf = new Pdf();
                $pdf->setCurrent(0);
                $stream = fopen($archiveParam->getCurrentPdf()->getFile(), 'rb');
                $pdf->setFile(stream_get_contents($stream));
                $pdf->setTitle($archiveParam->generateFileName('.pdf'));
                $pdf->addArchive($newArchive);
                $newArchive->addPdfSource($pdf);
            }

            $currentVersion = $archiveParam->getVersions()->last();
            $numero = $currentVersion->getNumero()+ 0.1;
            var_dump(count($currentVersion->getArchives()));
            $currentVersion->removeArchive($archiveParam);
            $archives = $currentVersion->getArchives();
            var_dump(count($currentVersion->getArchives()));
            $version = new Version();
            $version->setNumero($numero);
            //$version->addGroupArchives($archives);
            $version->setReseau($currentVersion->getReseau());
            $version->setEnCours(0);
            $version->addArchive($newArchive);
            $newArchive->addVersion($version);

            $em->persist($version);
            $em->flush();

            return $this->redirectToRoute('archiveparam_edit', array('id' => $archiveParam->getId()));
        }

        return $this->render('@Doc/archiveparam/edit.html.twig', array(
            'archiveParam' => $archiveParam,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ArchiveParam entity.
     *
     */
    public function deleteAction(Request $request, ArchiveParam $archiveParam)
    {
        $form = $this->createDeleteForm($archiveParam);
        $form->handleRequest($request);

        if (($form->isSubmitted() && $form->isValid()) || $request->isMethod('GET') ) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($archiveParam);
            $em->flush();
        }

        return $this->redirectToRoute('archiveparam_index');
    }

    /**
     * Creates a form to delete a ArchiveParam entity.
     *
     * @param ArchiveParam $archiveParam The ArchiveParam entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ArchiveParam $archiveParam)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('archiveparam_delete', array('id' => $archiveParam->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

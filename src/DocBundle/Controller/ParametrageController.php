<?php

namespace DocBundle\Controller;

use DateTime;
use DocBundle\Entity\ArchiveParam;
use DocBundle\Entity\ArchivePdf;
use DocBundle\Entity\Pdf;
use DocBundle\Entity\Reseau;
use DocBundle\Entity\Version;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $parametrages = $em->getRepository('DocBundle:Parametrage')->getParametrages();
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
            $parametrage->getPdfSource()->setTitle($parametrage->generateFileName('.pdf'));
            $stream = fopen($parametrage->getPdfSource()->getFile(), 'rb');
            $parametrage->getPdfSource()->SetFile(stream_get_contents($stream));
            $em = $this->getDoctrine()->getManager();

            $currentVersion = $parametrage->getReseau()->getVersions()->last();
            if (!$currentVersion->getEnCours()) {
                $version = new Version();
                $version->setEnCours('1');
                $version->setNumero($currentVersion->getNumero() + 1);
                $version->setReseau($parametrage->getReseau());
                $parametrage->getReseau()->addVersion($version);
            }

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
     * Finds and displays a Archive associated pdf document.
     *
     */
    public function showArchivePdfAction(ArchiveParam $parametrage)
    {
        $pdfFile = $parametrage->getPdf()->getFile();
        $response = new Response(stream_get_contents($pdfFile), 200, array('Content-Type' => 'application/pdf'));
        return $response;
    }

    /**
     * Displays a form to edit an existing Parametrage entity.
     *
     */
    public function editAction(Request $request, Parametrage $parametrage)
    {
        // TODO: Manage pdf data in updating form

        //var_dump(stream_get_contents($parametrage->getPdfSource()->getFile()));
        $deleteForm = $this->createDeleteForm($parametrage);
        $editForm = $this->createForm('DocBundle\Form\ParametrageType', $parametrage);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $parametrage->getPdfSource()->setTitle($parametrage->generateFileName('.pdf'));
            if($parametrage->getPdfSource()->getFile() !== null) {
                $stream = fopen($parametrage->getPdfSource()->getFile(), 'rb');
                $parametrage->getPdfSource()->setFile(stream_get_contents($stream));
                //var_dump(stream_get_contents($parametrage->getPdfSource()->getFile()));
            } /*else {
                $parametrage->getPdfSource()->setFile($old->getPdfSource()->getFile());
            }*/


            //$this->archive($old, $em, 'Modification');
            //$em->persist($parametrage);
            //$em->flush();

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
            if ($parametrage->getDeleting()) {
                $currentVersion = $parametrage->getReseau()->getVersions()->last();
                if (!$currentVersion->getEnCours()) {
                    $version = new Version();
                    $version->setEnCours('1');
                    $version->setNumero($currentVersion->getNumero() + 1);
                    $version->setReseau($parametrage->getReseau());
                    $parametrage->getReseau()->addVersion($version);
                }
                $id = $parametrage->getId();
                $em->remove($parametrage);
                $em->flush();
                return new JsonResponse(array(
                    'id' => $id,
                    'status' => 'deleted'
                ));
            }
            $parametrage->setDeleting(1);
            $em->persist($parametrage);
            $em->flush();
            return new JsonResponse(array(
                'id' => $parametrage->getId(),
                'status' => 'marked'
            ));
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
     * Deletes a Parametrage entity.
     *
     */
    public function cancelDeleteAction(Request $request, Parametrage $parametrage)
    {
        $em = $this->getDoctrine()->getManager();
        if ($parametrage == null) {
            throw $this->createNotFoundException("Le réseau  ".$parametrage.getId()." n'existe pas.");
        }
        if ($request->isMethod('GET')) {
            $parametrage->setDeleting(0);
            $em->persist($parametrage);
            $em->flush();
            return new JsonResponse(array(
                'id' => $parametrage->getId(),
                'status' => 'canceled'
            ));
        }
        return $this->render('DocBundle:parametrage:show.html.twig', array(
            'parametrage' => $parametrage
        ));
    }

    /**
     * Import a reseau params infos from CSV file.
     *
     */
    public function importCSVAction(Request $request)
    {

        $form = $this->createFormBuilder()
            ->add('submitFile', FileType::class, array('label' => 'Fichier CSV'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // TODO: Change source pdf file path
            // TODO: Change pdf file names column in the CSV file
            $pdfDir = $this->container->getParameter('kernel.root_dir').'/../../LB/pdf';
            $file = $form->get('submitFile')->getData();
            $data = $this->csvToArray($file);
            $em = $this->getDoctrine()->getManager();
            foreach($data as $row )
            {
                $stream = fopen($pdfDir.'/'.$row['pdf_source'], 'rb');

                $param = new Parametrage();
                $pdfSource = new Pdf();

                $reseau = $em->getRepository('DocBundle:Reseau')->findOneByCode($row['reseaux']);
                if (null === $reseau) {
                    throw $this->createNotFoundException("Le réseau ".$row['reseaux']." n'existe pas.");
                }
                $param->setReseau($reseau);
                $pdfSource->setFile(stream_get_contents($stream));
                $pdfSource->setTitle($row['pdf_source']);
                $param->setPdfSource($pdfSource);

                $param->setContrat($row['contrat']);
                $param->setCollectivites($row['collectivites']);
                $param->setType($row['type']);
                $param->setLibelle($row['libelle']);
                $param->setPartenaires('Tous');
                $param->setOrdre($row['ordre']);
                $param->setCommentaire($row['commentaires']);
                $param->setReference($row['reference']);

                $em->persist($param);
            }
            $em->flush();
            return $this->redirectToRoute('parametrage_index');
        }

        return $this->render('DocBundle:parametrage:csv_form.html.twig', array(
            'form' => $form->createView(),
        ));
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
     * @param string $filename
     * @param string $delimiter
     * @return array|bool
     */
    protected function csvToArray($filename='', $delimiter=';')
    {
        if(!file_exists($filename) || !is_readable($filename))
        {
            return false;
        }
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (false !== ($row = fgetcsv($handle, 1000, $delimiter)))
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }


    /**
     * @param Parametrage $parametrage
     * @param $em
     */
    public function archive(Parametrage $parametrage, $em, $action)
    {
        $archive = new ArchiveParam();
        $pdf = new ArchivePdf();

        $archive->setIdParam($parametrage->getId());
        $archive->setContrat($parametrage->getContrat());
        $archive->setLibelle($parametrage->getLibelle());
        $archive->setType($parametrage->getType());

        $pdf->setTitle($parametrage->getPdfSource()->getTitle());
        $pdf->setFile($parametrage->getPdfSource()->getFile());
        $archive->setPdf($pdf);
        $archive->setPartenaires($parametrage->getPartenaires());
        $archive->setCollectivites($parametrage->getCollectivites());
        $archive->setOrdre($parametrage->getOrdre());
        $archive->setReference($parametrage->getReference());
        $archive->setAction($action);
        $archive->setCreatedAt(new DateTime());
        $em->persist($archive);
    }
}

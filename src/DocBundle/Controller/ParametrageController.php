<?php

namespace DocBundle\Controller;


use DocBundle\Entity\ArchivePdf;
use DocBundle\Entity\Pdf;
use DocBundle\Entity\Version;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DocBundle\Entity\Parametrage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Finder\Finder;

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
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $maxPerPage=30;
        $parametrages = $em->getRepository('DocBundle:Parametrage')->getParametrages($page, $maxPerPage);
        $maxPerPage = ceil(count($parametrages)/$maxPerPage);
        if ($page > $maxPerPage) {
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }
        return $this->render('DocBundle:parametrage:index.html.twig', array(
            'parametrages' => $parametrages,
            'maxPerPage' => $maxPerPage,
            'page' => $page,
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
            $em = $this->getDoctrine()->getManager();
            $pdf = new Pdf();

            if ($parametrage->isLiasse()) {
                $parametrage->setType('LIASSE');
                $pdf = $this->composerLiasse($parametrage, $request, $em);
            } else {
                $stream = fopen($parametrage->getCurrentPdf()->getFile(), 'rb');
                $pdf->SetFile(stream_get_contents($stream));
            }
            $pdf->setCurrent(1);
            $pdf->setTitle($parametrage->generateFileName('.pdf'));
            $pdf->setParametrage($parametrage);
            $parametrage->addPdfSource($pdf);

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
     * @param Parametrage $parametrage
     * @param Request $request
     * @param $em
     * @return Pdf
     */
    public function composerLiasse (Parametrage $parametrage, Request $request, $em) {
        $pdf = new Pdf();
        //TODO: Repertoire en dur à enlever (parameters.yml)
        $mergingDir = $this->container->getParameter('kernel.root_dir').'/../../generations/tmp';
        $pythonDir = $this->container->getParameter('kernel.root_dir').'/../src/python/compo.py';
        $fs = new Filesystem();
        $fs->mkdir($mergingDir);
        $liassePdf = $request->request->get('pdfs');
        sort($liassePdf);
        $pdfs = $em->getRepository('DocBundle:Pdf')->findById($liassePdf);
        foreach ($pdfs as $p) {
            file_put_contents(
                $mergingDir .'/'. $request->request->get($p->getId()).'.pdf',
                $p->getFile()
            );
        }
        //TODO: Revoir l'exécution du script python (parameters.yml)
        $cmd = 'python "'.$pythonDir.'" "'.$mergingDir.'" "'.$mergingDir.'/'.$parametrage->generateFileName('.pdf').'"';
        $process = new Process($cmd);
        try {
            $process->mustRun();
            //echo $process->getOutput();
        } catch (ProcessFailedException $e) {
            throw $this->createNotFoundException("Erreur lors de la composition de la liasse. Paramétrage non créé");
        }
        $stream = fopen($mergingDir.'/'.$parametrage->generateFileName('.pdf'), 'rb');
        $pdf->SetFile(stream_get_contents($stream));
        fclose($stream);
        $fs->remove($mergingDir);

        return $pdf;
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
        $pdfFile = $parametrage->getLastPdfSource()->getFile();
        $response = new Response(stream_get_contents($pdfFile), 200, array('Content-Type' => 'application/pdf'));
        return $response;
    }

    /**
     * Displays a form to edit an existing Parametrage entity.
     *
     */
    public function editAction(Request $request, Parametrage $parametrage)
    {
        // TODO: Check performance
        $deleteForm = $this->createDeleteForm($parametrage);
        $editForm = $this->createForm('DocBundle\Form\ParametrageType', $parametrage);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $currentVersion = $parametrage->getReseau()->getVersions()->last();
            if (!$currentVersion->getEnCours()) {
                $version = new Version();
                $version->setEnCours('1');
                $version->setNumero($currentVersion->getNumero() + 1);
                $version->setReseau($parametrage->getReseau());
                $parametrage->getReseau()->addVersion($version);
            }

            if ($parametrage->isLiasse()) {
                $parametrage->setType('LIASSE');
                $pdf = $this->composerLiasse($parametrage, $request, $em);
                $pdf->setCurrent(1);
                $pdf->setTitle($parametrage->generateFileName('.pdf'));
                $pdf->setParametrage($parametrage);
                $parametrage->addPdfSource($pdf);
            } else {
                if($parametrage->getCurrentPdf()->getFile() != null) {
                    $pdf = new Pdf();
                    $pdf->setCurrent(1);
                    $stream = fopen($parametrage->getCurrentPdf()->getFile(), 'rb');
                    $pdf->setFile(stream_get_contents($stream));
                    $pdf->setTitle($parametrage->generateFileName('.pdf'));
                    $pdf->setParametrage($parametrage);
                    $parametrage->addPdfSource($pdf);
                } else {
                    $parametrage->getLastPdfSource()->setTitle($parametrage->generateFileName('.pdf'));
                    $parametrage->getLastPdfSource()->setCurrent(1);
                }
            }



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
        //TODO: Make version appear as "en cours" on delete confirmed
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
                    'status' => 'deleted',
                    'version' => $version->getNumero()
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
     * TODO: refactor this method for performance
     */
    public function importCSVAction(Request $request)
    {

        $form = $this->createFormBuilder()
            ->add('submitFile', FileType::class, array('label' => 'Fichier CSV'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            ini_set('max_execution_time', 0);
            // TODO: Change source pdf file path
            // TODO: Change pdf file names column in the CSV file
            $pdfDir = $this->container->getParameter('kernel.root_dir').'/../../ressources/pdf';
            $file = $form->get('submitFile')->getData();
            $data = $this->csvToArray($file);
            $em = $this->getDoctrine()->getManager();
            foreach($data as $row )
            {
                $stream = fopen($pdfDir.'/'.$row['contrat'].'/'.$row['pdf_source'], 'rb');
                $param = new Parametrage();
                $pdfSource = new Pdf();
                $pdfSource->setCurrent(1);
                $reseau = $em->getRepository('DocBundle:Reseau')->findOneByCode($row['reseaux']);
                if (null === $reseau) {
                    throw $this->createNotFoundException("Le réseau ".$row['reseaux']." n'existe pas.");
                }
                $param->setReseau($reseau);
                $pdfSource->setFile(stream_get_contents($stream));
                $pdfSource->setTitle($row['pdf_source']);
                $pdfSource->setParametrage($param);
                $param->addPdfSource($pdfSource);

                $param->setContrat($row['contrat']);
                $param->setCollectivites($row['collectivites']);
                $param->setType($row['type']);
                $param->setLibelle($row['libelle']);
                $param->setPartenaires('Tous');
                $param->setOrdre($row['ordre']);
                $param->setReference($row['reference']);

                $em->persist($param);
            }
            $em->flush();
            return $this->redirectToRoute('parametrage_index');
        }
        ini_set('max_execution_time', 60);
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
     *
     */
    public function getLiassePdfAction(Request $request) {
        $ref = $request->request->get('reference');
        $cont = $request->request->get('contrat');
        $em = $this->getDoctrine()->getManager();
        $param = $em->getRepository('DocBundle:Parametrage')->findLiassePdf($ref, $cont);
        $data = array();
        foreach ($param as $p) {
            $data[] = $this->getPdfInfos($p);
        }
        return new JsonResponse($data);
    }

    private function getPdfInfos(Parametrage $parametrage) {
        $e = array(
            'pdf_id' => $parametrage->getLastPdfSource()->getId(),
            'pdf' => $parametrage->getLastPdfSource()->getTitle()
        );
        return $e;
    }
}

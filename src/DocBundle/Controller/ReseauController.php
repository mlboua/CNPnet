<?php

namespace DocBundle\Controller;

use DateTime;
use DocBundle\Entity\ArchiveParam;
use DocBundle\Entity\ArchivePdf;
use DocBundle\Entity\Parametrage;
use DocBundle\Entity\Version;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DocBundle\Entity\Reseau;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

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
            $versioin = new Version();
            $versioin->setReseau($reseau);
            $versioin->setNumero('0');
            $versioin->setEnCours('1');
            $em->persist($versioin);
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
     * TODO: change todo request type to post
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historicalAction() {
        $em = $this->getDoctrine()->getManager();

        $reseaus = $em->getRepository('DocBundle:Reseau')->findAll();

        return $this->render('DocBundle:reseau:history_menu.html.twig', array(
            'reseaux' => $reseaus,
        ));
    }

    /**
     * Finds and displays a Reseau parametrages list.
     *
     */
    public function showParametrageAction(Reseau $reseau, $page)
    {
        /*if ($page < 1) {
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }*/
        $maxPerPage=25;

        $version = $reseau->getVersions()->last();
        $form = $this->createForm('DocBundle\Form\VersionType',
            $version,
            array('action' => $this->generateUrl('reseau_generate_params', ['id' => $reseau->getId()]
            ))
        );

        $exportForm = $this->createReseauForm($reseau, 'reseau_export_params', 'POST');
        $em = $this->getDoctrine()->getManager();
        $parametrage = $em->getRepository('DocBundle:Parametrage')->getParametrageByReseauNoPdf($reseau->getId(), $page, $maxPerPage);
        $maxPerPage = ceil(count($parametrage)/$maxPerPage);

        /*if ($page > $maxPerPage) {
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }*/

        return $this->render('DocBundle:reseau:reseau_params.html.twig', array(
            'parametrages' => $parametrage,
            'reseau' => $reseau,
            'version' => $version,
            'form' => $form->createView(),
            'exportForm' => $exportForm->createView(),
            'page' => $page,
            'maxPerPage' => $maxPerPage,
        ));
    }

    /**
     * Generate a reaseau params.
     * TODO: Check that files are successfully created
     *
     */
    public function generateParamsAction(Request $request, Reseau $reseau, Version $version)
    {
        $version = $reseau->getVersions()->last();
        $form = $this->createForm('DocBundle\Form\VersionType', $version);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            ini_set('max_execution_time', 0);
            $version->setUser($this->getUser()->getUsername());
            $reseauParamsDir = $this->container->getParameter('generation_dir').'/'.$reseau->getCode();
            $em = $this->getDoctrine()->getManager();

            $parametrages = $em->getRepository('DocBundle:Parametrage')->getParametrageByReseau($reseau->getId());
            $block = 1;
            $range = 20;
            $fs = new Filesystem();
            if ($fs->exists($reseauParamsDir)) {
                $fs->remove($reseauParamsDir);
            }
            while ($block < count($parametrages)) {
                $parametrages = $em->getRepository('DocBundle:Parametrage')->getParametrageByReseau($reseau->getId(), $block, $range);
                foreach ($parametrages as $param) {
                    try {
                        $contratDir = $reseauParamsDir .'/'. $param->getContrat();
                        $fs->mkdir($contratDir);
                        file_put_contents(
                            $contratDir .'/'. $param->getLastPdfSource()->getTitle(),
                            $param->getLastPdfSource()->getFile()
                        );
                        $this->generateCollectiviteFile($param, $version->getNumero(),
                            $version->getMessage(),
                            $contratDir .'/'. str_replace('.pdf', '.collectivites', $param->getLastPdfSource()->getTitle())
                        );
                    } catch (IOException $e) {
                        echo 'Une erreur est survenue lors de la création du repertoire '.$e->getPath();
                    }

                    $archive = new ArchiveParam();
                    $pdf = $param->getLastPdfSource();
                    $pdf->setCurrent(0);
                    $archive->addPdfSource($pdf);
                    $archive->setParametrage($param);
                    $archive->setAction("Génération");

                    $version->addArchive($archive);
                    $archive->addVersion($version);

                    $version->setEncours('0');
                    $reseau->addVersion($version);
                    $em->persist($reseau);
                    $em->flush();
                    $archive = null;
                }

                $block = $block + 1;
            }
            ini_set('max_execution_time', 60);
            return $this->redirectToRoute('reseau_show_parametrage',
                array(
                    'id' => $reseau->getId(),
                    'confirmation' => true
                )
            );
        }

        return new JsonResponse(array(
            'statut' => 'Vous devez passer par le formulaire de génération'
        ));
    }

    /**
     * Generate a reaseau params.
     * TODO: Check that files are successfully created
     *
     */
    public function exportParamsAction(Request $request, Reseau $reseau)
    {
        $iterableResult = $reseau->getParametrages();
        $handle = fopen('php://memory', 'r+');
        $header = array(
            'contrat',
            'reseaux',
            'collectivites',
            'ordre',
            'libelle',
            'pdf',
            'type',
            'reference',
            'pdf_source'
        );
        fputcsv($handle, $header, ';');
        foreach ($iterableResult as $row) {
            fputcsv($handle, array(
                    $row->getContrat(),
                    $reseau->getCode(),
                    $row->getCollectivites(),
                    $row->getOrdre(),
                    $row->getLibelle(),
                    $row->getLastPdfSource()->getTitle(),
                    $row->getType(),
                    $row->getReference(),
                    $row->getLastPdfSource()->getTitle()
                ),
                ';'
            );
        rewind($handle);
        $content = stream_get_contents($handle);
    }
        fclose($handle);

        return new Response($content, 200, array(
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export.csv"'
        ));
    }

    /**
     * @param Parametrage $parametrage
     * @param $path
     */
    protected function generateCollectiviteFile(Parametrage $parametrage, $version, $commentaire, $path)
    {
        $now = new DateTime();
        $now->format('d/m/Y H:i:s');
        $content = "#CREE AUTOMATIQUEMENT LE ".$now->format('d/m/Y H:i:s');
        $content .= "\n#Utilisateur : ".$this->getUser()->getUsername()."( ".$this->getUser()->getEmail().")";
        $content .= "\n# Version V".$version;
        $content .= "\n# $commentaire";
        $content .= "\n#----------------------------- ";
        $content .= "\nLIBELLE=". $parametrage->getLibelle();
        $content .= "\nORDRE=". $parametrage->getOrdre();
        $content .= "\nPARTENAIRE=";
        $content .= in_array($parametrage->getPartenaires(), array('*', 'tous', 'Tous')) ? '*' : $parametrage->getPartenaires();
        $content .= "\nCOLLECTIVITES=";
        $content .= in_array($parametrage->getCollectivites(), array('*', 'toutes', 'Toutes')) ? '*' : $parametrage->getCollectivites();
        file_put_contents($path,$content);
    }

    /**
     * Creates a form to delete a Reseau entity.
     *
     * @param Reseau $reseau The Reseau entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createReseauForm(Reseau $reseau, $actionPath, $method)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl($actionPath, array('id' => $reseau->getId())))
            ->setMethod($method)
            ->getForm()
        ;
    }


    /**
     * Creates a form to delete a Parametrage entity.
     *
     * @param Reseau $parametrage The Parametrage entity
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

<?php

namespace DocBundle\Controller;

use DateTime;
use DocBundle\Entity\Parametrage;
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
        $form = $this->createReseauForm($reseau, 'reseau_generate_params', 'POST');
        $exportForm = $this->createReseauForm($reseau, 'reseau_export_params', 'POST');
        $em = $this->getDoctrine()->getManager();
        $parametrage = $em->getRepository('DocBundle:Parametrage')->getParametrageWithReseau($reseau->getId());
        //$parametrage = $reseau->getParametrages();

        return $this->render('DocBundle:reseau:reseau_params.html.twig', array(
            'parametrages' => $parametrage,
            'reseau' => $reseau,
            'form' => $form->createView(),
            'exportForm' => $exportForm->createView()
        ));
    }

    /**
     * Generate a reaseau params.
     * TODO: Check that files are successfully created
     *
     */
    public function generateParamsAction(Request $request, Reseau $reseau)
    {
        $reseauParamsDir = $this->container->getParameter('kernel.root_dir').'/../../ressources/'.$reseau->getCode();
        $em = $this->getDoctrine()->getManager();
        $parametrages = $em->getRepository('DocBundle:Parametrage')->getParametrageWithReseau($reseau->getId());

        $fs = new Filesystem();

        foreach ($parametrages as $param) {
            try {
                $contratDir = $reseauParamsDir .'/'. $param->getContrat();
                $fs->mkdir($contratDir);
                file_put_contents(
                    $contratDir .'/'. $param->getPdfSource()->getTitle(),
                    $param->getPdfSource()->getFile()
                );
                $this->generateCollectiviteFile($param, $contratDir .'/'. $param->getPdfSource()->getTitle() .'.collectivites');
            } catch (IOException $e) {
                echo 'Une erreur est survenue lors de la création du repertoire '.$e->getPath();
            }
        }
        return new JsonResponse('Génération des paramètrages terminée');
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
            'pdf_source',
            'commentaires'
        );
        fputcsv($handle, $header, ';');
        foreach ($iterableResult as $row) {
            fputcsv($handle, array(
                    $row->getContrat(),
                    $reseau->getCode(),
                    $row->getCollectivites(),
                    $row->getOrdre(),
                    $row->getLibelle(),
                    $row->getPdfSource()->getTitle(),
                    $row->getType(),
                    $row->getReference(),
                $row->getPdfSource()->getTitle(),
                    $row->getCommentaire()
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
    protected function generateCollectiviteFile(Parametrage $parametrage, $path)
    {
        $now = new DateTime();
        $now->format('d/m/Y H:i:s');
        $content = "#CREE AUTOMATIQUEMENT LE ".$now->format('d/m/Y H:i:s');
        $content .= "\n#----------------------------- ";
        $content .= "\nLIBELLE=". $parametrage->getLibelle();
        $content .= "\nORDRE=". $parametrage->getOrdre();
        $content .= "\nPARTENAIRE=";
        $content .= in_array($parametrage->getPartenaires(), array('*', 'tous', 'Tous')) ? '*' : $parametrage->getPartenaires();
        $content .= "\nCOLLECTIVITES=";
        $content .= in_array($parametrage->getCollectivites(), array('*', 'tous', 'Tous')) ? '*' : $parametrage->getCollectivites();
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

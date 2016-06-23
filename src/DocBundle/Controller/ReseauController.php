<?php

namespace DocBundle\Controller;

use DocBundle\Entity\Parametrage;
use DocBundle\Entity\Reseau;
use DocBundle\Entity\Version;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 
 * @author scouliba
 *
 * Reseau controller.
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
     * 
     * @param Request $request
     * 
     * Creates a new Reseau entity.
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
     * 
     * @param Reseau $reseau
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * Finds and displays a Reseau entity.
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
     * 
     * @param Request $request
     * @param Reseau $reseau
     * 
     * Displays a form to edit an existing Reseau entity.
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
     * 
     * @param Request $request
     * @param Reseau $reseau
     * 
     * TODO: change todo request type to post
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
     * 
     * @param Reseau $reseau
     * @param unknown $page
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * Finds and displays a Reseau parametrages list.
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
     * 
     * @param Request $request
     * @param Reseau $reseau
     * @param Version $version
     * @return RedirectResponse | JsonResponse
     */
    public function generateParamsAction(Request $request, Reseau $reseau, Version $version) {
    	
    	$paramService = $this->get ( "param_service" );
    	
        $version = $reseau->getVersions()->last();
        $form = $this->createForm('DocBundle\Form\VersionType', $version);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $version->setUser($this->getUser()->getUsername());
            $reseauParamsDir = $this->container->getParameter('generation_dir').'/'.$reseau->getCode();
            $em = $this->getDoctrine()->getManager();

            $parametrages = $em->getRepository('DocBundle:Parametrage')->getParametrageByReseau($reseau->getId());
            
            $paramService->generateParams($reseau, $version, $parametrages, $reseauParamsDir);
            
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
     * 
     * @param Request $request
     * @param Reseau $reseau
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * TODO: Check that files are successfully created
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
     * Creates a form to delete a Reseau entity.
     *
     * @param Reseau $reseau The Reseau entity
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

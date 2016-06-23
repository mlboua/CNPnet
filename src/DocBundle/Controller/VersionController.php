<?php

/**
 * @author scouliba
 * Date: 26/01/2016
 * Time: 12:03
 */
namespace DocBundle\Controller;

use DocBundle\Entity\ArchiveParam;
use DocBundle\Entity\Reseau;
use DocBundle\Entity\Version;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VersionController extends Controller {
	/**
	 * Generate a reaseau params.
	 *
	 * @param Request $request        	
	 * @param Reseau $reseau        	
	 * @param Version $version        	
	 * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function generateParamsAction(Request $request, Reseau $reseau, Version $version) {
		$paramService = $this->get ( "param_service" );
		
		$version = $reseau->getVersions ()->last ();
		$form = $this->createForm ( 'DocBundle\Form\VersionType', $version );
		$form->handleRequest ( $request );
		
		if ($form->isSubmitted () && $form->isValid ()) {
			ini_set ( 'max_execution_time', 0 );
			$version->setUser ( $this->getUser ()->getUsername () );
			$reseauParamsDir = $this->container->getParameter ( 'generation_dir' ) . '/' . $reseau->getCode ();
			$em = $this->getDoctrine ()->getManager ();
			
			$parametrages = $em->getRepository ( 'DocBundle:ArchiveParam' )->getArchivesByVersion ( $version );
			
			$paramService->generateArchives ( $version, $parametrages, $reseauParamsDir );
			
			return $this->redirectToRoute ( 'reseau_show_parametrage', [ 
					'id' => $reseau->getId () 
			] );
		}
		
		return new JsonResponse ( array (
				'statut' => 'Vous devez passer par le formulaire de génération' 
		) );
	}
	
	/**
	 * Find and displays the version of a specific reseau.
	 *
	 * @param Request $request        	
	 * @param Reseau $reseau        	
	 */
	public function paramsHistoryAction(Request $request, Reseau $reseau) {
		$em = $this->getDoctrine ()->getManager ();
		$versions = $em->getRepository ( 'DocBundle:Version' )->getClosedByReseau ( $reseau );
		return $this->render ( "DocBundle:reseau:history.html.twig", array (
				'versions' => $versions,
				'reseau' => $reseau 
		) );
	}
	
	/**
	 * Finds and displays a parametrage list of a specific version.
	 *
	 * @param Version $version        	
	 * @return Response
	 */
	public function versionHistoryAction(Version $version, $page) {
		$reseau = $version->getReseau ();
		$form = $this->createForm ( 'DocBundle\Form\VersionType', $version, array (
				'action' => $this->generateUrl ( 'reseau_generate_archive_params', [ 
						'id' => $reseau->getId () 
				] ) 
		) );
		$exportForm = $this->createVersionForm ( $version, 'version_export_params', 'POST' );
		$em = $this->getDoctrine ()->getManager ();
		$maxPerPage = 30;
		$parametrages = $em->getRepository ( 'DocBundle:ArchiveParam' )->getArchivesByVersion ( $version, $page, $maxPerPage );
		$maxPerPage = ceil ( count ( $parametrages ) / $maxPerPage );
		if ($page > $maxPerPage) {
			// throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		return $this->render ( 'DocBundle:version:version_params.html.twig', array (
				'parametrages' => $parametrages,
				'reseau' => $reseau,
				'version' => $version,
				'form' => $form->createView (),
				'exportForm' => $exportForm->createView (),
				'maxPerPage' => $maxPerPage,
				'page' => $page 
		) );
	}
	
	/**
	 * Finds and displays a Archive associated pdf document.
	 *
	 * @param ArchiveParam $parametrage        	
	 */
	public function showArchivePdfAction(ArchiveParam $parametrage) {
		$pdfFile = $parametrage->getLastPdfSource ()->getFile ();
		$response = new Response ( stream_get_contents ( $pdfFile ), 200, array (
				'Content-Type' => 'application/pdf' 
		) );
		return $response;
	}
	
	/**
	 * Create an download csv file from table content.
	 *
	 * @param Request $request        	
	 * @param Version $version        	
	 * @return Response
	 */
	public function exportHarchiveParamsAction(Request $request, Version $version) {
		$iterableResult = $version->getArchives ();
		$handle = fopen ( 'php://memory', 'r+' );
		$header = array (
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
		fputcsv ( $handle, $header, ';' );
		foreach ( $iterableResult as $row ) {
			fputcsv ( $handle, array (
					$row->getContrat (),
					$version->getReseau ()->getCode (),
					$row->getCollectivites (),
					$row->getOrdre (),
					$row->getLibelle (),
					$row->getPdfSource ()->getTitle (),
					$row->getType (),
					$row->getReference (),
					$row->getPdfSource ()->getTitle () 
			), ';' );
			rewind ( $handle );
			$content = stream_get_contents ( $handle );
		}
		fclose ( $handle );
		
		return new Response ( $content, 200, array (
				'Content-Type' => 'application/force-download',
				'Content-Disposition' => 'attachment; filename="export.csv"' 
		) );
	}
	
	/**
	 *
	 * @param Version $version        	
	 * @param unknown $actionPath        	
	 * @param unknown $method        	
	 */
	private function createVersionForm(Version $version, $actionPath, $method) {
		return $this->createFormBuilder ()->setAction ( $this->generateUrl ( $actionPath, array (
				'id' => $version->getId () 
		) ) )->setMethod ( $method )->getForm ();
	}
}
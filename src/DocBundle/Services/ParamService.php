<?php

namespace DocBundle\Services;

use DateTime;
use DocBundle\Entity\ArchiveParam;
use DocBundle\Entity\Parametrage;
use DocBundle\Entity\Reseau;
use DocBundle\Entity\Version;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Filesystem\Filesystem;

class ParamService {
	
	/**
	 *
	 * @var EntityManager
	 */
	private $em;
	public function __construct(EntityManager $manager) {
		$this->em = $manager;
	}
	
	/**
	 * Generate a reaseau params.
	 *
	 * @param Reseau $reseau        	
	 * @param Version $version        	
	 * @param unknown $parametrages        	
	 * @param string $reseauParamsDir        	
	 */
	public function generateParams(Reseau $reseau, Version $version, $parametrages, $reseauParamsDir) {
		$block = 1;
		$range = 20;
		$fs = new Filesystem ();
		if ($fs->exists ( $reseauParamsDir )) {
			try {
				$fs->remove ( $reseauParamsDir );
			} catch ( IOException $e ) {
				echo "Imposible de supprimer le repertoire " + $reseauParamsDir + " raison :" + $e->getMessage ();
			}
		}
		ini_set ( 'max_execution_time', 0 );
		while ( $block < count ( $parametrages ) ) {
			$parametrages = $this->em->getRepository ( 'DocBundle:Parametrage' )->getParametrageByReseau ( $reseau->getId (), $block, $range );
			foreach ( $parametrages as $param ) {
				try {
					$contratDir = $reseauParamsDir . '/' . $param->getContrat ();
					$fs->mkdir ( $contratDir );
					file_put_contents ( $contratDir . '/' . $param->getLastPdfSource ()->getTitle (), $param->getLastPdfSource ()->getFile () );
					$collectiviteFileName = $contratDir . '/' . str_replace ( '.pdf', '.collectivites', $param->getLastPdfSource ()->getTitle () );
					$this->generateCollectiviteFile ( $param, $version, $version->getMessage (), $collectiviteFileName );
				} catch ( IOException $e ) {
					echo 'Une erreur est survenue lors de la création du repertoire ' . $e->getPath ();
				}
				
				$archive = new ArchiveParam ();
				$pdf = $param->getLastPdfSource ();
				$pdf->setCurrent ( 0 );
				$archive->addPdfSource ( $pdf );
				$archive->setParametrage ( $param );
				$archive->setAction ( "Génération" );
				
				$version->addArchive ( $archive );
				$archive->addVersion ( $version );
				
				$version->setEncours ( '0' );
				$reseau->addVersion ( $version );
				$this->em->persist ( $reseau );
				$this->em->flush ();
				$archive = null;
			}
			
			$block = $block + 1;
		}
		ini_set ( 'max_execution_time', 60 );
	}
	
	/**
	 * 
	 * @param Version $version
	 * @param unknown $parametrages
	 * @param string $reseauParamsDir
	 */
	public function generateArchives(Version $version, $parametrages, $reseauParamsDir) {
		$block = 1;
		$range = 20;
		$fs = new Filesystem ();
		if ($fs->exists ( $reseauParamsDir )) {
			try {
				$fs->remove ( $reseauParamsDir );
			} catch ( IOException $e ) {
				echo "Imposible de supprimer le repertoire " + $reseauParamsDir + " raison :" + $e->getMessage ();
			}
		}
		ini_set ( 'max_execution_time', 0 );
		while ( $block < count ( $parametrages ) ) {
			$parametrages = $this->em->getRepository ( 'DocBundle:ArchiveParam' )->getArchivesByVersion ( $version, $block, $range );
			foreach ( $parametrages as $param ) {
				try {
					$contratDir = $reseauParamsDir . '/' . $param->getContrat ();
					$fs->mkdir ( $contratDir );
					file_put_contents ( $contratDir . '/' . $param->getLastPdfSource ()->getTitle (), $param->getLastPdfSource ()->getFile () );
					$this->generateCollectiviteFile ( $param, $version, $version->getMessage (), $contratDir . '/' . str_replace ( '.pdf', '.collectivites', $param->getLastPdfSource ()->getTitle () ) );
				} catch ( IOException $e ) {
					echo 'Une erreur est survenue lors de la création du repertoire ' . $e->getPath ();
				}
			}
			$block = $block + 1;
		}
		$this->em->persist ( $version );
		$this->em->flush ();
		ini_set ( 'max_execution_time', 60 );
	}
	
	/**
	 *
	 * @param Parametrage $parametrage        	
	 * @param Version $version        	
	 * @param unknown $commentaire        	
	 * @param unknown $path        	
	 */
	protected function generateCollectiviteFile($parametrage, Version $version, $commentaire, $path) {
		$now = new DateTime ();
		$now->format ( 'd/m/Y H:i:s' );
		$content = "#CREE AUTOMATIQUEMENT LE " . $now->format ( 'd/m/Y H:i:s' );
		$content .= "\n#Utilisateur : " . $version->getUser (); // . "( " . $version->getUser ()->getEmail () . ")";
		$content .= "\n# Version V" . $version->getNumero ();
		$content .= "\n# $commentaire";
		$content .= "\n#----------------------------- ";
		$content .= "\nLIBELLE=" . $parametrage->getLibelle ();
		$content .= "\nORDRE=" . $parametrage->getOrdre ();
		$content .= "\nPARTENAIRE=";
		$content .= in_array ( $parametrage->getPartenaires (), array (
				'*',
				'tous',
				'Tous' 
		) ) ? '*' : $parametrage->getPartenaires ();
		$content .= "\nCOLLECTIVITES=";
		$content .= in_array ( $parametrage->getCollectivites (), array (
				'*',
				'toutes',
				'Toutes' 
		) ) ? '*' : $parametrage->getCollectivites ();
		file_put_contents ( $path, $content );
	}
}
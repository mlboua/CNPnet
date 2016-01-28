<?php
/**
 * User: scouliba
 * Date: 26/01/2016
 * Time: 12:03
 */

namespace DocBundle\Controller;


use DateTime;
use DocBundle\Entity\ArchiveParam;
use DocBundle\Entity\Version;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DocBundle\Entity\Reseau;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class VersionController extends Controller
{
    /**
     * Generate a reaseau params.
     * TODO: Check that files are successfully created
     * @param Request $request
     * @param Reseau $reseau
     * @param Version $version
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function generateParamsAction(Request $request, Reseau $reseau, Version $version)
    {
        $version = $reseau->getVersions()->last();
        $form = $this->createForm('DocBundle\Form\VersionType', $version);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $version->setUser($this->getUser()->getUsername());
            $reseauParamsDir = $this->container->getParameter('kernel.root_dir').'/../../ressources/'.$reseau->getCode();
            $em = $this->getDoctrine()->getManager();

            $parametrages = $em->getRepository('DocBundle:ArchiveParam')->getArchivesByVersion($version);

            $fs = new Filesystem();
            $fs->remove($reseauParamsDir);
            foreach ($parametrages as $param) {
                try {
                    $contratDir = $reseauParamsDir .'/'. $param->getContrat();
                    $fs->mkdir($contratDir);
                    file_put_contents(
                        $contratDir .'/'. $param->getPdfSource()->getTitle(),
                        $param->getPdfSource()->getFile()
                    );
                    $this->generateCollectiviteFile($param, $version->getNumero(), $version->getMessage(), $contratDir .'/'. $param->generateFileName('.collectivites'));
                } catch (IOException $e) {
                    echo 'Une erreur est survenue lors de la création du repertoire '.$e->getPath();
                }
            }
            return $this->redirectToRoute('reseau_show_parametrage', ['id' => $reseau->getId()]);
        }

        return new JsonResponse(array(
            'statut' => 'Vous devez passer par le formulaire de génération'
        ));
    }

    /**
     * @param ArchiveParam $parametrage
     * @param $version
     * @param $commentaire
     * @param $path
     */
    protected function generateCollectiviteFile(ArchiveParam $parametrage, $version, $commentaire, $path)
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
     * Finds and displays the version  of a specific reseau.
     *
     */
    public function paramsHistoryAction(Request $request, Reseau $reseau)
    {
        $em = $this->getDoctrine()->getManager();
        $versions = $em->getRepository('DocBundle:Version')->getClosedByReseau($reseau);
        return $this->render("DocBundle:reseau:history.html.twig", array(
            'versions' => $versions,
            'reseau' => $reseau
        ));
    }

    /**
     * Finds and displays a parametrage list of a specific version.
     * @param Version $version
     * @return Response
     */
    public function versionHistoryAction(Version $version)
    {
        $reseau = $version->getReseau();
        $form = $this->createForm('DocBundle\Form\VersionType',
            $version,
            array('action' => $this->generateUrl('reseau_generate_archive_params', ['id' => $reseau->getId()]
            ))
        );
        $exportForm = $this->createReseauForm($reseau, 'reseau_export_params', 'POST');
        $em = $this->getDoctrine()->getManager();
        $parametrages = $em->getRepository('DocBundle:ArchiveParam')->getArchivesByVersion($version);

        return $this->render('DocBundle:version:version_params.html.twig', array(
            'parametrages' => $parametrages,
            'reseau' => $reseau,
            'version' => $version,
            'form' => $form->createView(),
            'exportForm' => $exportForm->createView()
        ));
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

}
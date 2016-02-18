<?php

namespace DocBundle\Repository;

use DocBundle\Entity\Version;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * ArchiveParamRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArchiveParamRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Version $version
     * @param int $page
     * @param int $maxPerPage
     * @return array
     */
    public function getArchivesByVersion(Version $version, $page = 1, $maxPerPage=20)
    {
        $qb = $this->createQueryBuilder('ar');
        $qb->join('ar.versions', 'v')
            ->addSelect('v')
            ->where('v.id = :id')
            ->setParameter('id', $version->getId());
        $qb->setFirstResult(($page-1) * $maxPerPage)
            ->setMaxResults($maxPerPage);
        return new Paginator($qb, true);
    }
}

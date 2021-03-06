<?php

namespace DocBundle\Repository;

/**
 * ReseauRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReseauRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function getAll()
    {
        $qb = $this->createQueryBuilder('r');
        $qb->join('r.versions', 'v')
            ->addSelect('v')
            ->orderBy('r.name', 'ASC');
        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function getWithLastVersion()
    {
        $qb = $this->createQueryBuilder('r');
        $qb->join('r.versions', 'v')
            ->addSelect('v')
            ->orderBy('v.numero', 'DESC')
            ->setMaxResults(1);
        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function getCurrentVersion()
    {
        $qb = $this->createQueryBuilder('r');
        $qb->join('r.versions', 'v')
            ->addSelect('v')
            ->orderBy('v.numero', 'DESC')
            ->setMaxResults(1);
        return $qb
            ->getQuery()
            ->getResult();
    }
}

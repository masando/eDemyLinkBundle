<?php

namespace eDemy\LinkBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LinkRepository extends EntityRepository
{
    public function findLastModified($namespace = null)
    {
        $qb = $this->createQueryBuilder('l');
        if($namespace == null) {
            $qb->andWhere('l.namespace is null');
        } else {
            $qb->andWhere('l.namespace = :namespace');
            $qb->setParameter('namespace', $namespace);
        }
        $qb->orderBy('l.updated','DESC');
        
        $qb->setMaxResults(1);
        $query = $qb->getQuery();

        return $query->getSingleResult();
    }
}

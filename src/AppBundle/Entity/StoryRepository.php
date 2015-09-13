<?php
/**
 * Story Repository
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StoryRepository extends EntityRepository
{
    public function findAllOrderedByDate()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->orderBy('s.created', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findTopPosts($limit = 3)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->orderBy('s.created', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findRecentPosts($limit = 3)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->orderBy('s.created', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}

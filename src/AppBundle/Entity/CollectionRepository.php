<?php
/**
 * Collection Repository
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CollectionRepository extends EntityRepository
{
    public function getListWithCounter()
    {
        $qb = $this->getEntityManager()->createQueryBuilder('AppBundle:Collection', 'c');
        return $qb->select('c, COUNT(s.id) as total')
            ->from('AppBundle:Collection', 'c')
            ->leftJoin('c.stories', 's')
            ->having('COUNT(s.id) > 0')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
}

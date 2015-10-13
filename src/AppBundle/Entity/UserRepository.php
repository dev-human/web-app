<?php
/**
 * User Repository
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getAuthorsList()
    {
        $qb = $this->getEntityManager()->createQueryBuilder('AppBundle:User', 'u');

        return $qb->select('u')
            ->from('AppBundle:User', 'u')
            ->leftJoin('u.stories', 's')
            ->having('COUNT(s.id) > 0')
            ->groupBy('u.id')
            ->getQuery()
            ->getResult();
    }
}

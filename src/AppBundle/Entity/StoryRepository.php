<?php
/**
 * Story Repository
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class StoryRepository extends EntityRepository
{
    /**
     * @param string $author Author username
     * @param string $slug   Post slug
     * @return array
     */
    public function findOneFromAuthorAndSlug($author, $slug)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->join('s.author', 'a')
            ->where('s.slug = ?1')
            ->andWhere('a.username = ?2')
            ->setParameter(1, $slug)
            ->setParameter(2, $author)
            ->getQuery()
            ->getOneOrNullResult();
    }

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

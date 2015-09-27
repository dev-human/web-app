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
            ->orderBy('s.views', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findFeaturedPost()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $result = $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->where('s.featured = 1')
            ->orderBy('s.created', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        if (count($result)) {
            return $result[0];
        }

        return null;
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

    /**
     * Improve this :)
     * @param int $post Post ID
     * @param int $limit
     * @return array
     */
    public function findRelatedPosts($post, $limit = 3)
    {
        return $this->findRecentPosts($limit);
    }
}

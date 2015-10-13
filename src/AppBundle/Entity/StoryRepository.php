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
            ->where('s.published = 1')
            ->andwhere('s.slug = ?1')
            ->andWhere('a.username = ?2')
            ->setParameter(1, $slug)
            ->setParameter(2, $author)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFromCollection($collectionId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->join('s.collection', 'c')
            ->where('s.published = 1')
            ->andWhere('s.listed = 1')
            ->andwhere('c.id = ?1')
            ->orderBy('s.created', 'DESC')
            ->setParameter(1, $collectionId);
    }

    public function findFromTag($tagId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->join('s.tags', 't')
            ->where('s.published = 1')
            ->andWhere('s.listed = 1')
            ->andwhere('t.id = ?1')
            ->orderBy('s.created', 'DESC')
            ->setParameter(1, $tagId);
    }

    public function findFromAuthor($authorId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->join('s.author', 'a')
            ->where('s.published = 1')
            ->andwhere('a.id = ?1')
            ->orderBy('s.created', 'DESC')
            ->setParameter(1, $authorId);
    }

    public function findAllFromAuthor($authorId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->join('s.author', 'a')
            ->andwhere('a.id = ?1')
            ->orderBy('s.created', 'DESC')
            ->setParameter(1, $authorId);
    }

    public function findAllOrderedByDate()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->where('s.published = 1')
            ->andWhere('s.listed = 1')
            ->orderBy('s.created', 'DESC');
    }

    public function searchPosts($search)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $query =  $qb->select('s')
        ->from('AppBundle:Story', 's');

        return $query
                ->where('s.published = 1')
                ->andWhere($query->expr()->like('s.title', "'%" . $search . "%'"))
                ->orderBy('s.created', 'DESC');
    }

    public function findTopPosts($limit = 3)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->where('s.published = 1')
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
            ->where('s.published = 1')
            ->andwhere('s.featured = 1')
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
            ->where('s.published = 1')
            ->andWhere('s.listed = 1')
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
        $postObj = $this->find($post);

        if (!$postObj) {
            return null;
        }

        $qb = $this->getEntityManager()->createQueryBuilder();

        return $qb->select('s')
            ->from('AppBundle:Story', 's')
            ->join('s.collection', 'c')
            ->where('s.published = 1')
            ->andWhere('s.listed = 1')
            ->andWhere($qb->expr()->not('s.id = ?1'))
            ->andwhere('c.id = ?2')
            ->orderBy('s.created', 'DESC')
            ->setMaxResults($limit)
            ->setParameter(1, $postObj->getId())
            ->setParameter(2, $postObj->getCollection()->getId())
            ->getQuery()
            ->getResult();
    }
}

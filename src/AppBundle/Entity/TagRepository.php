<?php
/**
 * Tag Repository
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
    /**
     * Find a single tag matching a name or slug
     * @param $name
     * @return array
     */
    public function findTagByName($name)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from('AppBundle:Tag', 't');

        $result = $query
            ->where($query->expr()->like('t.name', "'%" . strtolower($name) . "%'"))
            ->getQuery()
            ->getResult();

        if (count($result)) {
            return $result[0];
        }

        return $result;
    }

    /**
     * Tries to find a tag, if it doesn't exist it will create a new tag and persist it to DB.
     * @param $tagName
     * @return array|Tag
     */
    public function getExistingOrCreateNew($tagName)
    {
        $tag = $this->findTagByName($tagName);

        if (!$tag) {
            $tag = new Tag();
            $tag->setName($tagName);
            $this->getEntityManager()->persist($tag);
            $this->getEntityManager()->flush();
        }

        return $tag;
    }
}

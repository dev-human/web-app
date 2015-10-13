<?php
/**
 * Article Importer
 */

namespace AppBundle\Service;

use AppBundle\Entity\Collection;
use AppBundle\Entity\Story;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Yaml\Yaml;

class ArticleImporterService
{
    /** @var  Registry $doctrine */
    protected $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param string $file Path to the .md file to import
     * @return Story
     */
    public function extractStory($file)
    {
        $articleContent = file_get_contents($file);
        $parts = explode('---', $articleContent);

        $meta    = Yaml::parse($parts[1]);
        $content = $parts[2];
        $date    = $this->getDate($file);

        $story = new Story();
        $story->setTitle($meta['title']);
        $story->setContent($content);
        $story->setCreated($date);
        $story->setUpdated($date);
        $story->setContentChanged($date);
        $story->setAuthor($this->getAuthor($meta));
        $story->setSlug($this->getSlug($file));
        $story->setCollection($this->getCollection($meta));
        $story->setTags($this->getTags($meta));
        $story->setViews(0);
        $story->setPublished(true);

        return $story;
    }

    /**
     * @param string $file
     * @return \DateTime
     */
    protected function getDate($file)
    {
        $filename = basename($file);

        return new \DateTime(substr($filename, 0, 10));
    }

    /**
     * @param string $file
     * @return string
     */
    protected function getSlug($file)
    {
        $parts = explode('.', basename($file));

        return strtolower(substr($parts[0], 11));
    }

    /**
     * @param array $meta
     * @return User
     */
    protected function getAuthor(array $meta)
    {
        $username = $meta['authors'][0];

        $em = $this->getDoctrine();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($username);

        if (!$user) {
            $user = new User();
            $user->setUsername($username);
            $user->setName($username);
            $this->getDoctrine()->persist($user);
        }

        return $user;
    }

    /**
     * Stories can be in 1 collection only, but they can have multiple tags.
     * This will get the first category and return a collection from it (existing or new)
     * @param array $meta
     * @return Collection
     */
    protected function getCollection(array $meta)
    {
        if (count($meta['categories'])) {
            $category = $meta['categories'][0];

            $em = $this->getDoctrine();
            $collection = $em->getRepository('AppBundle:Collection')->findOneByName($category);

            if (!$collection) {
                $collection = new Collection();
                $collection->setName($category);
                $this->getDoctrine()->persist($collection);
            }

            return $collection;
        }

        return null;
    }

    /**
     * @param array $meta
     * @return array
     */
    protected function getTags(array $meta)
    {
        $tags = [];
        foreach ($meta['tags'] as $tag) {
            $tags[] = $this->getTag($tag);
        }

        return $tags;
    }

    /**
     * @param string $name
     * @return Tag
     */
    protected function getTag($name)
    {
        $em = $this->getDoctrine();
        $tag = $em->getRepository('AppBundle:Tag')->findOneByName($name);

        if (!$tag) {
            $tag = new Tag();
            $tag->setName($name);
            $this->getDoctrine()->persist($tag);
        }

        return $tag;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager|object
     */
    protected function getDoctrine()
    {
        return $this->doctrine->getManager();
    }
}

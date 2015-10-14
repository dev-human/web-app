<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use \DateTime;
use Michelf\MarkdownExtra;

/**
 * Class Story
 * @ORM\Entity(repositoryClass="StoryRepository")
 */
class Story
{
    /**
     * @var int
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="stories")
     */
    protected $author;

    /**
     * @var string
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=128, unique=true)
     */
    protected $slug;

    /**
     * @var Collection
     * @ORM\ManyToOne(targetEntity="Collection", inversedBy="stories")
     */
    protected $collection;

    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist"})
     */
    protected $tags;

    /** @var string */
    public $tagsList;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $preview;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @var bool Published
     * @ORM\Column(type="boolean")
     */
    protected $published = 0;

    /**
     * Whether or not this story is included in the public listings
     * @var int
     * @ORM\Column(type="boolean")
     */
    protected $listed = 1;

    /**
     * @var bool Featured
     * @ORM\Column(type="boolean")
     */
    protected $featured = 0;

    /**
     * @var bool Comments Enabled (default yes)
     * @ORM\Column(type="boolean")
     */
    protected $commentsEnabled = 1;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @var \DateTime $contentChanged
     *
     * @ORM\Column(name="content_changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"title", "body"})
     */
    protected $contentChanged;

    /**
     * @var int $views
     *
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    protected $views = 0;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param Collection $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * @param string $preview
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     */
    public function setCreated(DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     */
    public function setUpdated(DateTime $updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return DateTime
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * @param DateTime $contentChanged
     */
    public function setContentChanged(DateTime $contentChanged)
    {
        $this->contentChanged = $contentChanged;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags = [])
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }
    }

    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function setViews($views)
    {
        $this->views = $views;
    }


    public function addView()
    {
        $this->views++;
    }

    /**
     * @return string
     */
    public function getHTMLContent()
    {
        return MarkdownExtra::defaultTransform($this->getContent());
    }

    /**
     * @return string
     */
    public function getTextOnlyContent()
    {
        $content = $this->getHTMLContent();
        //$content = preg_replace('/<h(.*?)>(.*?)<\/h(.*?)>/', '', $content);
        $content = strip_tags($content);
        //$content = trim(str_replace("\n", ' ', $content));
        //$content = trim(str_replace("\r", ' ', $content));
        //$content = str_replace("  ", " ", $content);

        return $content;
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return int
     */
    public function isListed()
    {
        return $this->listed;
    }

    /**
     * @param int $listed
     */
    public function setListed($listed)
    {
        $this->listed = $listed;
    }

    /**
     * @return boolean
     */
    public function isFeatured()
    {
        return $this->featured;
    }

    /**
     * @param boolean $featured
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;
    }

    /**
     * @return boolean
     */
    public function commentsEnabled()
    {
        return $this->commentsEnabled;
    }

    /**
     * @param boolean $value
     */
    public function setCommentsEnabled($value)
    {
        $this->commentsEnabled = $value;
    }

    /**
     * @param Tag $tag
     */
    public function removeTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            return;
        }

        $this->tags->removeElement($tag);
    }

    /**
     * clears tags
     */
    public function clearTags()
    {
        if (count($this->getTags())) {
            foreach ($this->getTags() as $tag) {
                $this->removeTag($tag);
            }
        }
    }

    /**
     * @return string
     */
    public function getTagsList()
    {
        $tags = [];

        foreach ($this->tags as $tag) {
            $tags[] = $tag->getName();
        }

        return implode(', ', $tags);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}

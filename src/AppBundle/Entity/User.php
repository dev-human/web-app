<?php
/**
 * User
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HeidiLabs\SauthBundle\Model\AbstractUser;

/**
 * Class User
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table="dh_user"
 */
class User extends AbstractUser
{
    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Story", mappedBy="author")
     */
    protected $stories;

    /**
     * @ORM\Column(type="text")
     */
    protected $bio;

    /**
     * @ORM\Column(type="string")
     */
    protected $websiteUrl;

    /**
     * @ORM\Column(type="string")
     */
    protected $twitterUrl;

    /**
     * @ORM\Column(type="string")
     */
    protected $googlePlusUrl;

    /**
     * @ORM\Column(type="string")
     */
    protected $githubUrl;


    /**
     * @return mixed
     */
    public function getStories()
    {
        return $this->stories;
    }

    /**
     * @param mixed $stories
     */
    public function setStories($stories)
    {
        $this->stories = $stories;
    }

    /**
     * @return mixed
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param mixed $bio
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    /**
     * @return mixed
     */
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }

    /**
     * @param mixed $websiteUrl
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;
    }

    /**
     * @return mixed
     */
    public function getTwitterUrl()
    {
        return $this->twitterUrl;
    }

    /**
     * @param mixed $twitterUrl
     */
    public function setTwitterUrl($twitterUrl)
    {
        $this->twitterUrl = $twitterUrl;
    }

    /**
     * @return mixed
     */
    public function getGooglePlusUrl()
    {
        return $this->googlePlusUrl;
    }

    /**
     * @param mixed $googlePlusUrl
     */
    public function setGooglePlusUrl($googlePlusUrl)
    {
        $this->googlePlusUrl = $googlePlusUrl;
    }

    /**
     * @return mixed
     */
    public function getGithubUrl()
    {
        return $this->githubUrl;
    }

    /**
     * @param mixed $githubUrl
     */
    public function setGithubUrl($githubUrl)
    {
        $this->githubUrl = $githubUrl;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername();
    }
}

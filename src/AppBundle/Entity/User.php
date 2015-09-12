<?php
/**
 * User
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @ORM\Entity
 */
class User
{
    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Story", mappedBy="author")
     */
    protected $stories;

    /**
     * Username in the website. Will be the same as Github
     * @ORM\Column(type="string", length=25, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    protected $email;

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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
}

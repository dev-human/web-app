<?php
/**
 * Helpers
 */

namespace AppBundle\Twig;

use Doctrine\ORM\EntityManager;

class DevHumanExtension extends \Twig_Extension
{
    /** @var EntityManager  */
    protected $doctrine;

    public function __construct(EntityManager $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'devhuman_extension';
    }

    public function getGravatarFunction($email, $size)
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$size";

        return $url;
    }

    public function getCollections()
    {
        return $this->doctrine->getRepository('AppBundle:Collection')->findAll();
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('gravatar', [$this, 'getGravatarFunction']),
        ];
    }

    public function getGlobals()
    {
        return [
          'collection_list' => $this->getCollections()
        ];
    }
}

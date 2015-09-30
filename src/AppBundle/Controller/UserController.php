<?php
/**
 * User "Home"
 */

namespace AppBundle\Controller;

use AppBundle\Exception\StoryNotFoundException;
use AppBundle\Exception\UnauthorizedException;
use AppBundle\Form\StoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    /**
     * Home
     * @Route("/home", name="devhuman_userhome")
     */
    public function homeAction()
    {
        $user = $this->getUser();

        return $this->render('user/home.html.twig', [
            'user'   => $user
        ]);
    }
}

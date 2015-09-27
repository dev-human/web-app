<?php
/**
 * User "Home"
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{

    /**
     * Home
     * @Route("/home", name="devhuman_userhome")
     */
    public function homeAction()
    {
        $user = $this->getUser();

        return $this->render('default/home.html.twig', [
            'user'   => $user
        ]);
    }
}

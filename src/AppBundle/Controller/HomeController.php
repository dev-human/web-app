<?php
/**
 * User "Home"
 */

namespace AppBundle\Controller;

use AppBundle\Exception\StoryNotFoundException;
use AppBundle\Exception\UnauthorizedException;
use AppBundle\Form\StoryType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * Home
     * @Route("/home", name="devhuman_userhome")
     */
    public function homeAction()
    {
         return $this->render('user/home-default.html.twig', [

        ]);
    }

    /**
     * Home
     * @Route("/home/stories", name="devhuman_userstories")
     */
    public function storiesAction()
    {
        $user = $this->getUser();

        return $this->render('user/home-stories.html.twig', [
            'user'   => $user
        ]);
    }

    /**
     * Edit Profile Form
     * @Route("/home/settings", name="devhuman_user_settings")
     */
    public function settingsAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();
            //add flashbag msg
            //return $this->redirectToRoute('devhuman_userhome');
        }

        return $this->render('user/home-settings.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
<?php

namespace AppBundle\Controller;

use Michelf\MarkdownExtra;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * Index
     * @Route("/", name="devhuman_homepage")
     */
    public function indexAction(Request $request)
    {
        $doctrine = $this->getDoctrine();

        $data['stories'] = $doctrine->getRepository('AppBundle:Story')->findAllOrderedByDate();

        return $this->render('default/index.html.twig', $data);
    }

    /**
     * Shows the list of articles in a collection
     * @Route("/c/{slug}", name="devhuman_collection")
     */
    public function collectionAction($slug)
    {
        $doctrine = $this->getDoctrine();
        $collection = $doctrine->getRepository('AppBundle:Collection')->findOneBySlug($slug);

        return $this->render('story/collection.html.twig', [
            'collection' => $collection,
            'stories' => $collection->getStories()
        ]);
    }

    /**
     * Lists the Authors
     * @Route("/authors", name="devhuman_authors")
     */
    public function authorsAction()
    {
        $doctrine = $this->getDoctrine();
        $authors = $doctrine->getRepository('AppBundle:User')->findAll();

        return $this->render('default/authors.html.twig', [
            'authors'   => $authors
        ]);
    }

    /**
     * Shows Static Pages
     * @Route("/p/{page}", name="devhuman_page")
     */
    public function pagesAction($page)
    {
        $template = "pages/$page.html.twig";

        if (!$this->get('templating')->exists($template)) {
            throw new NotFoundHttpException("The requested content could not be found.");
        }

        return $this->render($template);
    }

    /**
     * Show the User/Author Profile
     * @Route("/~{user}", name="devhuman_user")
     */
    public function userProfileAction($user)
    {
        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository('AppBundle:User')->findOneByUsername($user);

        if (!$user) {
            throw new NotFoundHttpException("User not found.");
        }

        return $this->render('default/profile.html.twig', [
            'user'   => $user
        ]);
    }

    /**
     * @Route("/search", name="devhuman_search")
     */
    public function searchAction(Request $request)
    {
        $query = $request->query->get('q');

        if (!$query) {
            throw new \Exception("You need to provide a search parameter.");
        }


    }
}

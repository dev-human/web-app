<?php

namespace AppBundle\Controller;

use AppBundle\Exception\CollectionNotFoundException;
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

        $qbuilder = $doctrine->getRepository('AppBundle:Story')->findAllOrderedByDate();

        $paginator  = $this->get('knp_paginator');
        $stories = $paginator->paginate(
            $qbuilder,
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );

        return $this->render('default/index.html.twig', [
            'stories' => $stories
        ]);
    }

    /**
     * Shows the list of articles in a collection
     * @Route("/c/{slug}", name="devhuman_collection")
     */
    public function collectionAction($slug, Request $request)
    {
        $doctrine = $this->getDoctrine();
        $collection = $doctrine->getRepository('AppBundle:Collection')->findOneBySlug($slug);

        if (!$collection) {
            throw new CollectionNotFoundException("The requested collection could not be found.");
        }

        $qbuilder = $doctrine->getRepository('AppBundle:Story')->findFromCollection($collection->getId());

        $paginator  = $this->get('knp_paginator');
        $stories = $paginator->paginate(
            $qbuilder,
            $request->query->getInt('page', 1)/*page number*/,
            4/*limit per page*/
        );

        return $this->render('story/collection.html.twig', [
            'collection' => $collection,
            'stories'    => $stories
        ]);
    }

    /**
     * Shows the list of articles under a specific tag
     * @Route("/t/{slug}", name="devhuman_tag")
     */
    public function tagAction($slug, Request $request)
    {
        $doctrine = $this->getDoctrine();
        $tag = $doctrine->getRepository('AppBundle:Tag')->findOneBySlug($slug);

        if (!$tag) {
            throw new CollectionNotFoundException("The requested tag could not be found.");
        }

        $qbuilder = $doctrine->getRepository('AppBundle:Story')->findFromTag($tag->getId());

        $paginator  = $this->get('knp_paginator');
        $stories = $paginator->paginate(
            $qbuilder,
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );

        return $this->render('story/tag.html.twig', [
            'tag' => $tag,
            'stories'    => $stories
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
    public function userProfileAction($user, Request $request)
    {
        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository('AppBundle:User')->findOneByUsername($user);

        if (!$user) {
            throw new NotFoundHttpException("User not found.");
        }

        $qbuilder = $doctrine->getRepository('AppBundle:Story')->findFromAuthor($user->getId());
        $paginator = $this->get('knp_paginator');
        $stories = $paginator->paginate(
            $qbuilder,
            $request->query->getInt('page', 1)/*page number*/,
            4/*limit per page*/
        );

        return $this->render('default/profile.html.twig', [
            'user'    => $user,
            'stories' => $stories
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

        $qbuilder = $this->getDoctrine()->getManager()->getRepository('AppBundle:Story')->searchPosts($query);

        $paginator  = $this->get('knp_paginator');
        $stories = $paginator->paginate(
            $qbuilder,
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );

        return $this->render('default/search_results.html.twig', [
            'stories' => $stories,
            'query'   => $query
        ]);
    }
}

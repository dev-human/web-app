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
     * @Route("/", name="devhuman_homepage")
     */
    public function indexAction(Request $request)
    {
        $doctrine = $this->getDoctrine();

        $data['stories'] = $doctrine->getRepository('AppBundle:Story')->findAll();

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

        return $this->render('default/index.html.twig', [
            'stories' => $collection->getStories()
        ]);
    }

    /**
     * Shows an Article
     * @Route("/stories/{slug}", name="devhuman_show_article")
     */
    public function showArticleAction($slug)
    {
        $doctrine = $this->getDoctrine();
        $story = $doctrine->getRepository('AppBundle:Story')->findOneBySlug($slug);

        if (!$story) {
            throw new NotFoundHttpException("The requested article could not be found.");
        }

        $content = MarkdownExtra::defaultTransform($story->getContent());

        return $this->render('default/article.html.twig', [
            'story'   => $story,
            'content' => $content,
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
     * Show the User/Author Profile
     * @Route("/{user}", name="devhuman_user")
     */
    public function userProfileAction($user)
    {
        $doctrine = $this->getDoctrine();
        $user = $doctrine->getRepository('AppBundle:User')->findOneByUsername($user);

        if (!$user) {
            throw new NotFoundHttpException("User not found.");
        }

        return $this->render('profile/index.html.twig', [
            'user'   => $user
        ]);
    }
}

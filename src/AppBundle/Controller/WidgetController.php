<?php
/**
 * Sidebar Widgets Controller
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WidgetController extends Controller
{
    /**
     * Gets collections with counter number
     * @Route("/widget/collections", name="widget_collections")
     */
    public function collectionsWidgetAction()
    {
        $collections = $this->get('cache')->fetch($this->getParameter('key_cache_collections'));

        return $this->render(
            '_widgets/collections.html.twig',
            [
                'collections' => $collections
            ]
        );
    }

    /**
     * @Route("/widget/featured", name="widget_featured")
     */
    public function featuredPostWidgetAction()
    {

        return $this->render(
            '_widgets/featured.html.twig',
            [
                'featured' => ''            ]
        );
    }

    /**
     * @Route("/widget/facebook", name="widget_facebook")
     */
    public function facebookWidgetAction()
    {

        return $this->render(
            '_widgets/facebook.html.twig'
        );
    }

    /**
     * @Route("/widget/twitter", name="widget_twitter")
     */
    public function twitterWidgetAction()
    {

        return $this->render(
            '_widgets/twitter.html.twig'
        );
    }

    /**
     * @Route("/widget/tags", name="widget_tags")
     */
    public function tagsWidgetAction()
    {
        $tags = $this->get('cache')->fetch($this->getParameter('key_cache_tags'));

        return $this->render(
            '_widgets/tags.html.twig',
            [
                'tags' => $tags
            ]
        );
    }

    /**
     * Gets most recent and popular posts
     * @Route("/widget/topPosts", name="widget_topPosts")
     */
    public function topPostsAction($max = 3)
    {
        //$topPosts = $this->get('redis')->getSerializedCache($this->getParameter('key_cache_topstories'));

        $topPosts = $this->getDoctrine()->getRepository('AppBundle:Story')->findTopPosts($max);
        $recentPosts = $this->getDoctrine()->getRepository('AppBundle:Story')->findRecentPosts($max);

        return $this->render(
            '_widgets/topPosts.html.twig',
            [
                'topPosts'    => $topPosts,
                'recentPosts' => $recentPosts
            ]
        );
    }
}

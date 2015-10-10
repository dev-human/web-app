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
        $featured = $this->getDoctrine()->getRepository('AppBundle:Story')->findFeaturedPost();

        return $this->render(
            '_widgets/featured.html.twig',
            [
                'featured' => $featured
            ]
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
    public function tagsWidgetAction($max = 10)
    {
        $tags = $this->get('cache')->fetch($this->getParameter('key_cache_tags'));

        shuffle($tags);
        array_splice($tags, $max);

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
        $topPosts = $this->get('cache')->fetch($this->getParameter('key_cache_topstories'));
        if (!$topPosts) {
            $topPosts = $this->getDoctrine()->getRepository('AppBundle:Story')->findTopPosts(3);
            $this->get('cache')->save($this->getParameter('key_cache_topstories'), $topPosts);
        }

        $recentPosts = $this->getDoctrine()->getRepository('AppBundle:Story')->findRecentPosts($max);

        return $this->render(
            '_widgets/topPosts.html.twig',
            [
                'topPosts'    => $topPosts,
                'recentPosts' => $recentPosts
            ]
        );
    }

    /**
     * @param int $post ID
     * @param int $max
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/widget/relatedPosts/{post}", name="widget_relatedPosts")
     */
    public function relatedPostsWidgetAction($post, $max = 3)
    {
        $relatedPosts = $this->getDoctrine()->getRepository('AppBundle:Story')->findRelatedPosts($post, $max);

        return $this->render(
            '_widgets/relatedPosts.html.twig',
            [
                'relatedPosts'    => $relatedPosts
            ]
        );
    }
}

<?php
/**
 * Story Controller
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Exception\StoryNotFoundException;
use AppBundle\Exception\UnauthorizedException;
use AppBundle\Form\StoryType;
use Michelf\MarkdownExtra;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StoryController extends Controller
{
    /**
     * Returns converted markdown
     * @Route("/tools/preview", name="devhuman_preview_md")
     */
    public function markdownPreviewAction(Request $request)
    {
        $title = $request->get('title');
        $content = $request->get('content');

        if ($content) {
            $content = MarkdownExtra::defaultTransform($content);
        }

        return $this->render('story/preview_markdown.html.twig', [
            'content' => $content ?: "",
            'title'   => $title ?: "",
        ]);
    }

    /**
     * Edit Story Form
     * @Route("/s/{story}/edit", name="devhuman_edit_story")
     */
    public function editStoryAction($story, Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $theStory = $em->getRepository('AppBundle:Story')->find($story);

        if (!$theStory) {
            throw new StoryNotFoundException("The requested story could not be found.");
        }

        if ($theStory->getAuthor() !== $user) {
            throw new UnauthorizedException("You don't have permission to edit this story.");
        }

        $form = $this->createForm(new StoryType(), $theStory);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($theStory);
            $em->flush();

            return $this->redirectToRoute('devhuman_userhome');
        }

        return $this->render('story/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Shows an Article - alternative link
     * @Route("/s/{slug}", name="devhuman_show_article_alt")
     */
    public function showArticleAltAction($slug)
    {
        $doctrine = $this->getDoctrine();
        $story = $doctrine->getRepository('AppBundle:Story')->findOneFromSlug($slug);

        if (!$story) {
            throw new StoryNotFoundException("The requested story could not be found.");
        }

        return $this->redirectToRoute(
            'devhuman_show_article',
            ['slug' => $slug, 'author' => $story->getAuthor()->getUsername]
        );
    }

    /**
     * Shows an Article
     * @Route("/{author}/{slug}", name="devhuman_show_article")
     */
    public function showArticleAction($author, $slug)
    {
        $doctrine = $this->getDoctrine();
        $story = $doctrine->getRepository('AppBundle:Story')->findOneFromAuthorAndSlug($author, $slug);

        if (!$story) {
            throw new StoryNotFoundException("The requested story could not be found.");
        }

        $content = MarkdownExtra::defaultTransform($story->getContent());

        $story->addView();
        $doctrine->getManager()->persist($story);
        $doctrine->getManager()->flush();

        return $this->render('story/show.html.twig', [
            'story'    => $story,
            'content'  => $content,
        ]);
    }
}

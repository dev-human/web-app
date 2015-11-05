<?php
/**
 * Story Controller
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Story;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Exception\StoryNotFoundException;
use AppBundle\Exception\UnauthorizedException;
use AppBundle\Form\StoryType;
use Michelf\MarkdownExtra;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

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

            $this->handleFormTags($theStory);

            $em->persist($theStory);
            $em->flush();

            $this->addFlash(
                'success',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('devhuman_edit_story', ['story' => $story]);
        }

        return $this->render('story/form.html.twig', [
            'form'  => $form->createView(),
            'story' => $theStory
        ]);
    }

    /**
     * @Route("/s/create", name="devhuman_story_create")
     */
    public function createNewStoryAction(Request $request)
    {

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $story = new Story();
        $form = $this->createForm(new StoryType(), $story);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $story->setAuthor($user);
            $story->setCreated(new \DateTime());
            $this->handleFormTags($story);
            $em->persist($story);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf('Your new Story "%s" was successfully saved.', $story->getTitle())
            );

            return $this->redirectToRoute('devhuman_edit_story', ['story' => $story->getId()]);
        }

        return $this->render('story/form.html.twig', [
            'form'  => $form->createView(),
            'story' => $story
        ]);
    }

    /**
     * @param Story $story
     * @return Story
     */
    private function handleFormTags(Story $story)
    {
        $em = $this->getDoctrine()->getManager();
        $tagsList = explode(',', $story->tagsList);
        if (count($tagsList)) {

            if ($story->getId()) {
                $story->clearTags();
                $em->persist($story);
            }

            foreach ($tagsList as $tagName) {

                $name = trim(strtolower($tagName));
                if (empty($name)) {
                    continue;
                }

                $tag = $em->getRepository('AppBundle:Tag')->getExistingOrCreateNew($name);
                $story->addTag($tag);
            }
        }

        return $story;
    }

    /**
     * @Route("/s/{storyId}/remove", name="devhuman_story_remove")
     */
    public function removeStoryAction($storyId)
    {
        $user = $this->getUser();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $story = $em->getRepository('AppBundle:Story')->find($storyId);

        if (!$story) {
            throw new StoryNotFoundException("The requested story could not be found.");
        }

        if ($story->getAuthor() !== $user) {
            throw new UnauthorizedException("You don't have permission to delete this story.");
        }

        $em->remove($story);
        $em->flush();

        $this->addFlash(
            'success',
            sprintf('The story "%s" was successfully deleted.', $story->getTitle())
        );

        return $this->redirectToRoute('devhuman_userhome');
    }

    /**
     * Shows an Article - alternative link
     * @Route("/s/{slug}", name="devhuman_show_article_alt")
     */
    public function showArticleAltAction($slug)
    {
        $doctrine = $this->getDoctrine();
        $story = $doctrine->getRepository('AppBundle:Story')->findOneBySlug($slug);

        if (!$story) {
            throw new StoryNotFoundException("The requested story could not be found.");
        }

        return $this->redirectToRoute(
            'devhuman_show_article',
            ['slug' => $slug, 'author' => $story->getAuthor()->getUsername() ]
        );
    }

    /**
     * Shows an Article - another alternative link (shorter)
     * @Route("/s/{storyId}", name="devhuman_show_article_alt2")
     */
    public function showArticleAlt2Action($storyId)
    {
        $doctrine = $this->getDoctrine();
        $story = $doctrine->getRepository('AppBundle:Story')->find($storyId);

        if (!$story) {
            throw new StoryNotFoundException("The requested story could not be found.");
        }

        return $this->redirectToRoute(
            'devhuman_show_article',
            ['slug' => $story->getSlug(), 'author' => $story->getAuthor()->getUsername() ]
        );
    }

    /**
     * Shows an Article - old permalinks from dev-human.com
     * @Route("/entries/{year}/{month}/{day}/{slug}/", name="devhuman_show_article_old")
     */
    public function showArticleOldAction($year, $month, $day, $slug)
    {
        $doctrine = $this->getDoctrine();
        $story = $doctrine->getRepository('AppBundle:Story')->findOneBySlug($slug);

        if (!$story) {
            throw new StoryNotFoundException("The requested story could not be found.");
        }

        return $this->redirectToRoute(
            'devhuman_show_article',
            ['slug' => $slug, 'author' => $story->getAuthor()->getUsername() ]
        );
    }

    /**
     * Shows an Article
     * @Route("/~{author}/{slug}", name="devhuman_show_article")
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

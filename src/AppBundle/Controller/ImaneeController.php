<?php
/**
 * Imanee Utilities Controller
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Story;
use Imanee\ImageToolsBundle\HighlightCard;
use Imanee\ImageToolsBundle\SocialCard;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImaneeController extends Controller
{
    /**
     * Generates an image for social networks - Twitter
     * @Route("/tools/imanee/card/twitter/{storyId}", name="imanee_card_twitter")
     */
    public function generateTwitterCardAction($storyId, Request $request)
    {
        $story = $this->getDoctrine()->getManager()->getRepository('AppBundle:Story')->find($storyId);

        if ($story) {
            $title = $story->getTitle();
            $author = '@' . $story->getauthor()->getUsername();
        } else {
            $title = "Story not found.";
            $author = '---';
        }

        $twittercard = new SocialCard($title, $author, __DIR__ . '/../Resources/img/story-card.png');
        $card = $twittercard->generateCard();

        return new Response($card->output(), 200, [
            'Content-type' => 'image/jpg'
        ]);
    }

    /**
     * Generates an image for social networks - Facebook
     * @Route("/tools/imanee/card/facebook/{storyId}", name="imanee_card_facebook")
     */
    public function generateFacebookCardAction($storyId, Request $request)
    {
        $story = $this->getDoctrine()->getManager()->getRepository('AppBundle:Story')->find($storyId);

        if ($story) {
            $title = $story->getTitle();
            $author = '@' . $story->getauthor()->getUsername();
        } else {
            $title = "Story not found.";
            $author = '---';
        }

        $fbCard = new SocialCard($title, $author, __DIR__ . '/../Resources/img/story-card-fb.png');
        $fbCard->setTitleFontSize(60);
        $fbCard->setAuthorFontSize(40);

        $card = $fbCard->generateCard();

        return new Response($card->output(), 200, [
            'Content-type' => 'image/jpg'
        ]);
    }

    /**
     * Generates an image for social networks - Facebook
     * @Route("/tools/imanee/quote/{storyId}", name="imanee_quote")
     */
    public function generateQuoteAction($storyId, Request $request)
    {
        $story = $this->getDoctrine()->getManager()->getRepository('AppBundle:Story')->find($storyId);

        if (!$story) {
            throw new \Exception('Invalid story');
        }

        $quote = $request->query->get('quote');

        if (!$quote) {
            throw new \Exception('You must provide a quote.');
        }

        $card = new HighlightCard();
        $card->setQuoteAuthor($story->getAuthor()->getUsername());
        $card->setSourceLogo(__DIR__ . '/../Resources/img/dev-human-sticker.png');

        $image = $card->generateQuoteCard($quote, 506);

        return new Response($image->output(), 200, [
            'Content-type' => 'image/jpg'
        ]);
    }

    /**
     * Generates an image for social networks - Facebook
     * @Route("/quote/{storyId}", name="imanee_indexed_quote")
     */
    public function generateIndexedQuoteAction($storyId, Request $request)
    {
        /** @var Story $story */
        $story = $this->getDoctrine()->getManager()->getRepository('AppBundle:Story')->find($storyId);

        if (!$story) {
            throw new \Exception('Invalid story');
        }

        $q = $request->query->get('q');

        if (!$q) {
            throw new \Exception('Quote index was not provided.');
        }

        $index = explode(',', $q);

        $quote = substr(strip_tags($story->getHTMLContent()), $index[0], $index[1]);

        if (!$quote) {
            $quote = "Error";
        }

        $card = new HighlightCard();
        $card->setQuoteAuthor($story->getAuthor()->getUsername());
        $card->setSourceLogo(__DIR__ . '/../Resources/img/dev-human-sticker.png');

        $image = $card->generateQuoteCard($quote, 506);

        return new Response($image->output(), 200, [
            'Content-type' => 'image/jpg'
        ]);
    }
}

<?php
/**
 * Imanee Utilities Controller
 */

namespace AppBundle\Controller;

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
}

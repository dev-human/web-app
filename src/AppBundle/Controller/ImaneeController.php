<?php
/**
 * Imanee Utilities Controller
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Story;
use AppBundle\Entity\User;
use GuzzleHttp\Client;
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
        $quoteLimit = 260;

        $story = $this->getDoctrine()->getManager()->getRepository('AppBundle:Story')->find($storyId);

        if (!$story) {
            throw new \Exception('Invalid story');
        }

        $quote = $request->query->get('quote');

        if (!$quote) {
            throw new \Exception('You must provide a quote.');
        }

        if (strlen($quote) > $quoteLimit) {
            $quote = substr($quote, 0, $quoteLimit) . '...';
        }

        $titleLimit = 55;
        $title = $story->getTitle();
        if (strlen($title) > $titleLimit) {
            $title = substr($title, 0, 55) . '...';
        }

        $card = new HighlightCard();
        $card->setQuoteAuthor($story->getAuthor()->getUsername());
        $card->setSourceLogo(__DIR__ . '/../Resources/img/dev-human-sticker.png');
        $card->setQuoteSource($title);
        $card->setSourceUrl('dev-human.io/s/' . $storyId);

        if ($story->getAuthor()->getEmail()) {
            $avatar = $this->getCachedGravatar($story->getAuthor()->getEmail());
            $card->setQuoteAvatar($avatar);
        }

        $image = $card->generateQuoteCard($quote, 506);

        if ($request->query->has('download')) {
            $response = new Response();

            $response->headers->set('Cache-Control', 'public');
            $response->headers->set('Content-type', 'image/jpeg');
            $response->headers->set('Content-Disposition', 'attachment; filename="devhuman_quote.jpg";');

            $response->sendHeaders();
            $response->setContent($image->output());

            return $response;
        }

        return new Response($image->output(), 200, [
            'Cache-Control' => 'public',
            'Content-type' => 'image/jpg'
        ]);
    }

    /**
     * Generates an image for social networks - Facebook
     * @Route("/quote/{storyId}", name="imanee_indexed_quote")
     */
    public function generateIndexedQuoteAction($storyId, Request $request)
    {
        $quoteLimit = 210;

        /** @var Story $story */
        $story = $this->getDoctrine()->getManager()->getRepository('AppBundle:Story')->find($storyId);

        if (!$story) {
            throw new \Exception('Invalid story');
        }

        $q = $request->query->get('q');

        if (!$q) {
            throw new \Exception('Quote index was not provided.');
        }

        list ($start, $size) = explode(',', $q);

        $append = '';
        if ($size > $quoteLimit) {
            $append = '...';
            $size = $quoteLimit;
        }

        $content = $story->getTextOnlyContent();

        $quote = substr($content, $start, $size) . $append;

        if (!$quote) {
            $quote = "Error";
        }

        $titleLimit = 55;
        $title = $story->getTitle();
        if (strlen($title) > $titleLimit) {
            $title = substr($title, 0, 55) . '...';
        }

        $card = new HighlightCard();
        $card->setQuoteAuthor($story->getAuthor()->getUsername());
        $card->setSourceLogo(__DIR__ . '/../Resources/img/dev-human-sticker.png');
        $card->setQuoteSource($title);

        if ($story->getAuthor()->getEmail()) {
            $avatar = $this->getCachedGravatar($story->getAuthor()->getEmail());
            $card->setQuoteAvatar($avatar);
        }

        $image = $card->generateQuoteCard($quote, 506);

        return new Response($image->output(), 200, [
            'Content-type' => 'image/jpg'
        ]);
    }

    /**
     * Generates an image for social networks - Facebook
     * @Route("/quote/{storyId}.jpg", name="imanee_indexed_quote")
     */
    public function getIndexedQuoteAction($storyId, Request $request)
    {
        $q = $request->query->get('q');

        if (!$q) {
            throw new \Exception('Quote index was not provided.');
        }

        $cacheDir = __DIR__ . '/../../../app/data/images';
        $image = $cacheDir . '/' . md5($storyId . $q) . '.jpg';

        return new Response(file_get_contents($image), 200, [
            'Content-type' => 'image/jpg'
        ]);
    }

    /**
     * @param string $email
     * @return bool|string
     */
    public function getCachedGravatar($email)
    {
        $cacheDir = __DIR__ . '/../../../app/data/images';
        $file = md5($email);
        $image = $cacheDir . '/' . $file;

        if (!is_file($image)) {
            $guzzle = new Client();

            $url = 'http://www.gravatar.com/avatar/';
            $url .= md5(strtolower(trim($email)));
            $url .= "?s=60";

            $response = $guzzle->get($url);

            if ($response->getStatusCode() == 200) {
                $content = $response->getBody();

                if (!$this->saveCachedImageFile($image, $content)) {
                    return false;
                }
            }
        }

        return $image;
    }

    /**
     * @param string $path
     * @param string $contents
     * @return bool
     */
    private function saveCachedImageFile($path, $contents)
    {
        $fp = fopen($path, "w");
        if ($fp !== false) {
            fwrite($fp, $contents);
            fclose($fp);

            return true;
        }

        return false;
    }
}

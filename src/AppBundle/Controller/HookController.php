<?php
/**
 * Github Hooks
 */

namespace AppBundle\Controller;

use AppBundle\Service\ArticleImporterService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class HookController extends Controller
{
    protected $eventsLog = 'github_events.log';

    /**
     * Merged PR
     * @Route("/gh/events/push", name="devhuman_hook_merged")
     */
    public function pullRequestMergedCommand()
    {
        $legacyDir   = __DIR__ . '/../../../app/data/legacy';
        $articlesDir = $legacyDir . '/source/_posts';
        $this->writeLog("Push event received");

        shell_exec("cd $legacyDir && git pull 2>&1");

        /** @var ArticleImporterService $importer */
        $importer = $this->get('article.importer');

        foreach (glob($articlesDir . '/*.md') as $article) {
            $story = $importer->extractStory($article);

            $em = $this->getDoctrine()->getManager();

            //check for duplicates
            $storyCheck = $em->getRepository('AppBundle:Story')->findOneBySlug($story->getSlug());

            if (!$storyCheck) {
                $em->persist($story);
                $em->flush();

                $this->writeLog("Imported new article: " . $story->getTitle());
            }
        }

        return new Response('OK');
    }

    private function writeLog($message)
    {
        $logpath = __DIR__ . '/../../../app/logs/' . $this->eventsLog;

        $fp = fopen($logpath, "a+");
        if ($fp !== false) {
            fwrite($fp, date('[Y-m-d H:i:s]') . " $message.\n");
            fclose($fp);
        }
    }
}

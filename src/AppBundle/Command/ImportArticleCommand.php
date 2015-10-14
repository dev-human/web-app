<?php
/**
 * Imports an article in a markdown file ("old" posts from PRs)
 */

namespace AppBundle\Command;

use AppBundle\Entity\Collection;
use AppBundle\Entity\Story;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use AppBundle\Service\ArticleImporterService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ImportArticleCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('devhuman:import:file')
            ->setDescription('Import a markdown article from a file (Sculpin format)')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to a .md file with the article'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        if (!$file or !is_file($file)) {
            $output->writeln('<error>Invalid file.</error>');
        }

        /** @var ArticleImporterService $importer */
        $importer = $this->getContainer()->get('article.importer');
        $story = $importer->extractStory($file);

        $em = $this->getDoctrine();

        //check for duplicates
        $check = $em->getRepository('AppBundle:Story')->findOneBySlug($story->getSlug());

        if ($check) {
            $output->writeln(
                '<info>Story imported before - skipping.</info>'
            );

            return;
        }

        $em->persist($story);
        $em->flush();

        $output->writeln(
            '<info>Successfully imported the article "'.
            $story->getTitle() . '" from ' . $story->getAuthor()->getName() . '</info>'
        );
    }

    /**
     * @return EntityManager
     */
    protected function getDoctrine()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
}

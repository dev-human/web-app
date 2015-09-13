<?php
/**
 * Imports multiple markdown files ("old" posts from PRs)
 */

namespace AppBundle\Command;

use AppBundle\Entity\Collection;
use AppBundle\Entity\Story;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ImportArticlesFromDirCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('devhuman:import:dir')
            ->setDescription('Import multiple markdown articles from a directory (files in Sculpin format)')
            ->addArgument(
                'dir',
                InputArgument::REQUIRED,
                'Path to a directory containing .md files in Sculpin format'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');

        if (!$dir or !is_dir($dir)) {
            $output->writeln('<error>Invalid file.</error>');
        }

        $command = $this->getApplication()->find('devhuman:import:file');

        foreach (glob($dir . '/*.md') as $file) {

            $arguments = [
            'command' => 'devhuman:import:file',
                'file'    => $file
            ];

            $input = new ArrayInput($arguments);
            if ($command->run($input, $output)) {
                $output->writeln("<comment>Article imported: $file</comment>");
            };
        }

        $output->writeln("<info>Finished.</info>");
    }

    /**
     * @param string $file Path to the .md file to import
     * @return Story
     */
    protected function extractStory($file)
    {
        $articleContent = file_get_contents($file);
        $parts = explode('---', $articleContent);

        $meta    = Yaml::parse($parts[1]);
        $content = $parts[2];
        $date    = $this->getDate($file);

        $story = new Story();
        $story->setTitle($meta['title']);
        $story->setContent($content);
        $story->setCreated($date);
        $story->setUpdated($date);
        $story->setContentChanged($date);
        $story->setAuthor($this->getAuthor($meta));
        $story->setSlug($this->getSlug($file));
        $story->setTags($meta['tags']);
        $story->setCollections($this->getCollections($meta));

        return $story;
    }

    /**
     * @param string $file
     * @return \DateTime
     */
    protected function getDate($file)
    {
        $filename = basename($file);

        return new \DateTime(substr($filename, 0, 10));
    }

    /**
     * @param string $file
     * @return string
     */
    protected function getSlug($file)
    {
        $parts = explode('.', basename($file));

        return substr($parts[0], 11);
    }

    /**
     * @param array $meta
     * @return User
     */
    protected function getAuthor(array $meta)
    {
        $username = $meta['authors'][0];

        $em = $this->getDoctrine();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($username);

        if (!$user) {
            $user = new User();
            $user->setUsername($username);
            $user->setName($username);
            $this->getDoctrine()->persist($user);
        }

        return $user;
    }

    /**
     * @param array $meta
     * @return Collection[]
     */
    protected function getCollections(array $meta)
    {
        $collections = [];
        foreach ($meta['categories'] as $category) {
            $collections[] = $this->getCollection($category);
        }

        return $collections;
    }

    /**
     * @param string $name
     * @return Collection
     */
    protected function getCollection($name)
    {
        $em = $this->getDoctrine();
        $collection = $em->getRepository('AppBundle:Collection')->findOneByName($name);

        if (!$collection) {
            $collection = new Collection();
            $collection->setName($name);
            $this->getDoctrine()->persist($collection);
        }

        return $collection;
    }

    /**
     * @return EntityManager
     */
    protected function getDoctrine()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }
}

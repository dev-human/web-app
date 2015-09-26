<?php
/**
 * Updates Cache data and save to Redis
 */

namespace AppBundle\Command;

use AppBundle\Entity\Collection;
use AppBundle\Entity\Story;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class UpdateCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('devhuman:cache:update')
            ->setDescription('Updates Redis cache')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $cache = $this->getContainer()->get('cache');

        $output->writeln(
            '<comment>Updating Cache: Collections</comment>'
        );

        $collections = $em->getRepository('AppBundle:Collection')->getListWithCounter();
        $cache->save($this->getContainer()->getParameter('key_cache_collections'), $collections);

        $output->writeln(
            '<comment>Updating Cache: Top Tags</comment>'
        );

        $tags = $em->getRepository('AppBundle:Tag')->findAll();
        $cache->save($this->getContainer()->getParameter('key_cache_tags'), $tags);

        $output->writeln(
            '<comment>Updating Cache: Top Stories</comment>'
        );

        $top = $em->getRepository('AppBundle:Story')->findTopPosts(3);
        $cache->save($this->getContainer()->getParameter('key_cache_topstories'), $top);

        $output->writeln(
            '<info>Cache Successfully Updated.</info>'
        );
    }
}

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
            ->setName('devhuman:update:cache')
            ->setDescription('Updates cached data and save to Redis')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $redis = $this->getContainer()->get('redis');

        $output->writeln(
            '<comment>Updating Cache: Collections</comment>'
        );


        $output->writeln(
            '<comment>Updating Cache: Top Stories</comment>'
        );

        $output->writeln(
            '<info>Cache Successfully Updated.</info>'
        );
    }
}

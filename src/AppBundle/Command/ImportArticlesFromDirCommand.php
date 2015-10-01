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
            $output->writeln('<error>Invalid Directory.</error>');
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
}

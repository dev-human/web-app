<?php
/**
 * Imports authors from a yml file (https://github.com/dev-human/dev-human/blob/master/app/config/sculpin_site.yml)
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

class ImportAuthorsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('devhuman:import:authors')
            ->setDescription('Import authors from the original sculpin.yml file
            (https://github.com/dev-human/dev-human/blob/master/app/config/sculpin_site.yml)')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'Path to a yml file with the authors'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        if (!$file or !is_file($file)) {
            $output->writeln('<error>Invalid file.</error>');
        }
        $em = $this->getContainer()->get('doctrine')->getManager();

        $config = Yaml::parse(file_get_contents($file));
        $authors = $config['authors'];

        foreach ($authors as $username => $info) {
            $user = new User();
            $user->setUsername($username);
            $user->setName(isset($info['name']) ? $info['name'] : $username);
            $user->setBio(isset($info['bio']) ? $info['bio'] : "");
            $user->setWebsiteUrl(isset($info['url']) ? $info['url'] : "");
            $user->setGithubUrl(isset($info['github']) ? $info['github'] : "");
            $user->setTwitterUrl(isset($info['twitter']) ? $info['twitter'] : "");
            $user->setGooglePlusUrl(isset($info['googleplus']) ? $info['googleplus'] : "");

            $em->persist($user);
        }

        $em->flush();
        $output->writeln(
            '<info>Successfully imported</info>'
        );
    }
}

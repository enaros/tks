<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Emiliano
 * Date: 24.06.13
 * Time: 10:27
 * To change this template use File | Settings | File Templates.
 */

namespace Tks\TksBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Tks\TksBundle\Entity\Deployment;

class DeploymentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tks:create:deployment')
            ->setDescription('Create a new Deployment')
            ->addArgument('name', InputArgument::REQUIRED, 'Deployment name?')
            // ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = preg_replace("/[^a-z\.\-_A-Z0-9]+/", "", $input->getArgument('name'));
        // validate name
        if ($name != $input->getArgument('name')) {
            $output->writeln("\nERROR: Deployment name valid characters are [0-9 a-z . - _].\n");
            return;
        }

        $doctrine = $this->getContainer()->get('doctrine');
        $exists = $doctrine->getRepository('TksBundle:Deployment')->findBy(
            array(
                'name' => $name,
                'parent' => 0
            )
        );
        // validate already exists
        if ($exists) {
            $output->writeln("\nERROR: Deployment $name already exists, please choose a different name.\n");
            return;
        }

        $random = md5(microtime());

        $d = new Deployment();
        $d->setName($name);
        $d->setParent(0);
        $d->setApiToken($random);

        $em = $doctrine->getManager();
        $em->persist($d);
        $em->flush();

        $output->writeln("\nDeployment: $name was successfully created.\nApiToken: $random\n");
    }
}

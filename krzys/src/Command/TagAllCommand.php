<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TagAllCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('tagAll')
            ->setDescription('tag all pictures as 1 or 2')
            ->addArgument('whichTag', InputArgument::OPTIONAL, 'tag the one which were not taged')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $whichTag = $input->getArgument('whichTag');


        if ($input->getOption('limit')) {
            $limit = $input->getOption('limit');
        } else {
            $limit = 100000;
        }

        $output->writeln('Taged all files as: ' . $whichTag . ', limit set to: ' . $limit);

        $this->getContainer()->get('nailit_filesystem')->tagAll($this->getContainer()->getParameter('dir.years'), $whichTag, $limit);

    }

}

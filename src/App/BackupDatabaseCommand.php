<?php

namespace WpSyncer\App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupDatabaseCommand extends Command
{
	public function configure()
	{

		$this->setName('backup:db')
			->setDescription('Say hello!')
			->addArgument('name', InputArgument::OPTIONAL, 'Your name.', 'Test');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("<info>Backing up database</info>");
	}
}

<?php

namespace WpSyncer\Command;

use WpSyncer\App\App;
use WpSyncer\App\Backup\DatabaseBackup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupDatabaseCommand extends Command
{
	public function configure()
	{
		$this->setName('backup:db')
			->setDescription('Backup a remote database.')
			->addArgument('name', InputArgument::OPTIONAL, 'Database name', 'db.sql');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("<info>Backing up database</info>");
		$output->writeln("<info>Running from:</info> " . getcwd() . "\n");

		$remote = App::getRemote();
		$local = App::getLocal();

		$backup = new DatabaseBackup($remote, $local, $output);

		$backup
			->setName($input->getArgument('name'))
			->create()
			->download()
			->deleteFromRemote();

		return 1;
	}
}

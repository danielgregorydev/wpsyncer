<?php

namespace WpSyncer\Command;

use WpSyncer\App\App;
use WpSyncer\App\Backup\UploadsBackup;
use WpSyncer\App\Backup\DatabaseBackup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupUploadsCommand extends Command
{
	public function configure()
	{
		$this->setName('backup:uploads')
			->setDescription('Ensure the local uploads are in sync with the live site.');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("<info>Syncing uploads</info>");
		$output->writeln("<info>Running from:</info> " . getcwd() . "\n");

		$remote = App::getRemote();
		$local = App::getLocal();

		$backup = new UploadsBackup($remote, $local, $output);

		$backup
			->download();

		return 1;
	}
}

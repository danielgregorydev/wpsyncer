<?php

namespace WpSyncer\Command;

use Exception;
use WpSyncer\App\Env;
use WpSyncer\App\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WpSyncer\App\DatabaseBackup;

class BackupDatabaseCommand extends Command
{
	private Server $server;

	public function configure()
	{

		$this->setName('backup:db')
			->setDescription('Backup a remote database.')
			->addArgument('name', InputArgument::OPTIONAL, 'Database name', 'db.sql');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("<info>Backing up database</info>");
		$output->writeln("<info>Running from:</info> " . getcwd());

		$server = $this->setupServer();

		if (!$server->connected()) {
			throw new Exception('Cannot connect to remote server: Check the connection details are correct in your .env file and you have a ssh key associated with the server.');
		}

		$output->writeln('<info>Remote connection successful</info>');

		$backup = new DatabaseBackup($server, $output);

		$backup
			->setName($input->getArgument('name'))
			->create()
			->download()
			->deleteFromRemote();

		return 1;
	}

	public function setupServer()
	{
		$server = new Server(
			Env::get('SSH_USER'),
			Env::get('SSH_HOSTNAME'),
			Env::get('SSH_PORT')
		);

		$this->server = $server;

		return $server;
	}
}

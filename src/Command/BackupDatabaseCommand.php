<?php

namespace WpSyncer\Command;

use Exception;
use WpSyncer\App\Env;
use WpSyncer\App\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
		$dbName = $input->getArgument('name');
		$cdwp = 'cd ' . Env::get('REMOTE_WP_PATH');

		if (!$server->testConnection()) {
			throw new Exception('Cannot connect to remote server: Check the connection details are correct in your .env file and you have a ssh key associated with the server.');
		}

		$output->writeln('<info>Remote connection successful</info>');

		$steps = [
			[
				'commands' => ['wp'],
				'error' => 'wp-cli does not appear to be installed on the remote server.',
				'success' => 'Checked wp-cli is installed.'
			],
			[
				'commands' => [$cdwp, 'wp core is-installed'],
				'error' => 'WordPress does not appear to be installed. Check the provided remote directory is correct in your .env.',
				'success' => 'Confirmed current WordPress installation can be accessed'
			],
			[
				'commands' => [
					$cdwp,
					'wp db export ' . $dbName,
					'mv ' . $dbName . ' ../' . $dbName
				],
				'error' => 'Could not create database backup.',
				'success' => '<info>Remote database backed up</info>'
			],
		];

		foreach ($steps as $step) {
			if (!$this->server->execute($step['commands'])->isSuccessful()) {
				throw new Exception($step['error']);
			}

			$output->writeln($step['success']);
		}

		$output->writeln('<info>Beginning database download</info>');

		if (!$this->server->download($dbName, $dbName)->isSuccessful()) {
			$output->writeln('<error>Error downloading database</error>');
		} else {
			$output->writeln('<info>Database "' . $dbName . '" downloaded successfully</info>');
		}

		if (!$this->server->execute('rm ' . $dbName)->isSuccessful()) {
			throw new Exception('Could not delete database backup from remote.');
		}

		$output->writeln('<info>Deleted database backup from remote</info>');

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

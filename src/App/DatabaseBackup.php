<?php

namespace WpSyncer\App;

use Exception;
use WpSyncer\App\Server;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseBackup
{
	private Server $server;
	private OutputInterface $output;
	private $name = 'db.sql';

	public function __construct(Server $server, OutputInterface $output)
	{
		$this->server = $server;
		$this->output = $output;
	}

	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	public function create()
	{
		$cdwp = 'cd ' . $this->server->getWpInstallPath();
		$name = $this->name;

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
					'wp db export ' . $name,
					'mv ' . $name . ' ../' . $name
				],
				'error' => 'Could not create database backup.',
				'success' => '<info>Remote database backed up</info>'
			],
		];

		foreach ($steps as $step) {
			$success = $this->server->execute($step['commands'])->isSuccessful();

			$this->report($step[$success ? 'success' : 'error'], !$success);
		}

		return $this;
	}

	public function download()
	{
		$name = $this->name;

		$this->report('<info>Beginning database download</info>');

		$downloaded = $this->server->download($name, $name)->isSuccessful();

		$this->report(
			$downloaded
				? '<info>Database "' . $name . '" downloaded successfully</info>'
				: '<error>Error downloading database</error>'
		);

		return $this;
	}

	public function deleteFromRemote()
	{
		$deleted = $this->server->execute('rm ' . $this->name)->isSuccessful();

		$this->report(
			$deleted
				? '<info>Deleted database backup from remote</info>'
				: 'Could not delete database backup from remote.',
			!$deleted
		);

		return $this;
	}

	private function report($message, $isError = false)
	{
		if ($isError) {
			throw new Exception($message);
		}

		$this->output->writeln($message);

		return $this;
	}
}

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
		if (!$this->server->hasWpCli()) {
			$this->report("A database backup cannot be created when the server does not have wp-cli installed", 1);
			return;
		}

		$command = sprintf('wp db export %s --path=%s', $this->name, $this->server->getWpInstallPath());
		$success = $this->server->execute($command)->isSuccessful();

		$this->report(
			$success
				? '<info>Remote database backed up</info>'
				: 'Could not create database backup.',
			!$success
		);

		return $this;
	}

	public function download()
	{
		$name = $this->name;

		$this->report('<info>Beginning database download</info>');

		$downloaded = $this->server->download($name)->isSuccessful();

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

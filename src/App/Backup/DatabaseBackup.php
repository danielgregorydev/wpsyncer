<?php

namespace WpSyncer\App\Backup;

class DatabaseBackup extends Backup
{
	private $name = 'db.sql';

	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	public function create()
	{
		if (!$this->remote->hasWpCli()) {
			$this->report("A database backup cannot be created when the server does not have wp-cli installed", 1);
			return;
		}

		$command = sprintf('wp db export %s --path=%s', $this->name, $this->remote->getWpInstallPath());
		$success = $this->remote->execute($command)->isSuccessful();

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

		$downloaded = $this->remote->download($name)->isSuccessful();

		$this->report(
			$downloaded
				? '<info>Database "' . $name . '" downloaded successfully</info>'
				: '<error>Error downloading database</error>'
		);

		return $this;
	}

	public function deleteFromRemote()
	{
		$deleted = $this->remote->execute('rm ' . $this->name)->isSuccessful();

		$this->report(
			$deleted
				? '<info>Deleted database backup from remote</info>'
				: 'Could not delete database backup from remote.',
			!$deleted
		);

		return $this;
	}
}

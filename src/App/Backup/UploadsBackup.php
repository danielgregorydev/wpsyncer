<?php

namespace WpSyncer\App\Backup;

class UploadsBackup extends Backup
{
	public function download()
	{
		$this->report('Downloading uploads');

		$this->report($this->remote->uploadPath());
		$this->report($this->local->uploadPath() . '..');
		var_dump($this->remote->uploadPath() . ', ' . $this->local->uploadPath() . '/..');

		$success =
			$this->remote->download($this->remote->uploadPath(), $this->local->uploadPath() . '/..');

		$this->report($success->getOutput());
		$this->report(
			$success->isSuccessful()
				? '<info>Uploads downloaded</info>'
				: 'Failed to download uploads.',
			!$success->isSuccessful()
		);

		return $this;
	}
}

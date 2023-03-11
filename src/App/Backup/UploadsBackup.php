<?php

namespace WpSyncer\App\Backup;

class UploadsBackup extends Backup
{
	public function download()
	{
		$this->report('Downloading uploads');

		$success =
			$this->remote->download($this->remote->uploadPath(), $this->local->uploadPath() . '..');

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

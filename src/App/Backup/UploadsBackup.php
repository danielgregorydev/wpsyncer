<?php

namespace WpSyncer\App\Backup;

class UploadsBackup extends Backup
{
	public function download()
	{
		$this->report('Downloading uploads');

		$this->report($this->remote->uploadPath());
		$this->report($this->local->uploadPath() . '..');
		var_dump(
			preg_replace("/\r|\n/", "", $this->remote->uploadPath() . ', ' . $this->local->uploadPath() . '/..')
		);

		$success =
			$this->remote->download(
				preg_replace("/\r|\n/", "", $this->remote->uploadPath()),
				preg_replace("/\r|\n/", "", $this->local->uploadPath()) . '/..'
			);

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

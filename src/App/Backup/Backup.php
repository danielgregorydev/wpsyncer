<?php

namespace WpSyncer\App\Backup;

use Exception;
use WpSyncer\Model\Local;
use WpSyncer\Model\Remote;
use Symfony\Component\Console\Output\OutputInterface;

class Backup
{
	protected Remote $remote;
	protected Local $local;
	protected OutputInterface $output;

	public function __construct(Remote $remote, Local $local, OutputInterface $output)
	{
		$this->remote = $remote;
		$this->local = $local;
		$this->output = $output;
	}

	protected function report($message, $isError = false)
	{
		if ($isError) {
			throw new Exception($message);
		}

		$this->output->writeln($message);

		return $this;
	}
}

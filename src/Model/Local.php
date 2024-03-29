<?php

namespace WpSyncer\Model;

use Exception;
use WpSyncer\App\Env;

class Local extends Server
{
	public function __construct()
	{
	}

	public function commandPrefix()
	{
		return Env::get('LOCAL_COMMAND_PREFIX', '');
	}

	public function execute($command)
	{
		$prefix = $this->commandPrefix();
		$commands = is_array($command) ? $command : [$command];
		$responses = [];

		foreach ($commands as $c) {
			$responses[$c] = shell_exec($prefix . ' ' . $c);
		}

		return count($responses) === 1 ? join('', $responses) : $responses;
	}

	public function wp($command)
	{
		return $this->execute('wp ' . $command);
	}

	public function uploadPath()
	{
		return $this->wp('option get upload_path');
	}
}

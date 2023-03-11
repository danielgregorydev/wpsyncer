<?php

namespace WpSyncer\App;

use Exception;
use WpSyncer\Model\Local;
use WpSyncer\Model\Remote;

class App
{
	public static function getRemote()
	{
		$server = new Remote(
			Env::get('SSH_USER'),
			Env::get('SSH_HOSTNAME'),
			Env::get('SSH_PORT')
		);

		if (!$server->connected()) {
			throw new Exception('Cannot connect to remote server: Check the connection details are correct in your .env file and you have a ssh key associated with the server.');
		}

		return $server;
	}

	public static function getLocal()
	{
		$server = new Local();

		return $server;
	}
}

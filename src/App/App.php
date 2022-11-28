<?php

namespace WpSyncer\App;

use Exception;

class App
{
	public static function getServer()
	{
		$server = new Server(
			Env::get('SSH_USER'),
			Env::get('SSH_HOSTNAME'),
			Env::get('SSH_PORT')
		);

		if (!$server->connected()) {
			throw new Exception('Cannot connect to remote server: Check the connection details are correct in your .env file and you have a ssh key associated with the server.');
		}

		return $server;
	}
}

<?php

namespace WpSyncer\App;

use Exception;
use Symfony\Component\Dotenv\Dotenv;

class Env
{
	private static $prefix = 'WPSYNCER';

	public static function load()
	{
		$dotenv = new Dotenv();
		$dotenv->load(getcwd() . '/.env');
	}

	public static function get($key, $default = null)
	{
		$key = self::formatKey($key);

		if ($default === null && !isset($_ENV[$key])) {
			throw new Exception('Attempting to retrieve "' . $key . '" environment variable but it is not defined. Does the current directory have a .env?');
		}

		return $_ENV[$key] ?? $default;
	}

	public static function formatKey($key)
	{
		return self::$prefix . '_' . $key;
	}
}

<?php

namespace WpSyncer\App;

use Exception;
use Spatie\Ssh\Ssh;
use WpSyncer\App\Env;

class Server
{
	private string $user;
	private string $hostname;
	private int $port;
	private Ssh $ssh;

	public function __construct($user, $hostname, $port)
	{
		$this->user = $user;
		$this->hostname = $hostname;
		$this->port = $port;
		$this->ssh = $this->connect();
	}

	public function connect()
	{
		return Ssh::create($this->user, $this->hostname, $this->port)->disablePasswordAuthentication();
	}

	public function connected()
	{
		$process = $this->ssh->execute('pwd');

		return $process->isSuccessful();
	}

	public function execute($commands)
	{
		return $this->ssh->execute($commands);
	}

	public function download($remotePath, $localPath)
	{
		return $this->ssh->download($remotePath, $localPath);
	}

	public function getWpInstallPath()
	{
		$path = Env::get('REMOTE_WP_PATH');

		if (!$this->execute('wp core is-installed --path="' .  $path . '"')->isSuccessful()) {
			throw new Exception("WordPress does not appear to be installed. Check the provided remote directory is correct in your .env. Path provided was: " . $path);
		}

		return $path;
	}

	public function hasWpCli()
	{
		return $this->execute('wp')->isSuccessful();
	}
}

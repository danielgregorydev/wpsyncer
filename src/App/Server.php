<?php

namespace WpSyncer\App;

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
		return Env::get('REMOTE_WP_PATH');
	}

	public function hasWpCli()
	{
		return $this->execute('wp')->isSuccessful();
	}
}

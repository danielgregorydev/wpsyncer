<?php

namespace WpSyncer\App;

use Spatie\Ssh\Ssh;

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
	public function testConnection()
	{
		$process = $this->ssh->execute('pwd');

		return $process->isSuccessful();
	}

	public function connect()
	{
		return Ssh::create($this->user, $this->hostname, $this->port)->disablePasswordAuthentication();
	}

	public function execute($commands)
	{
		return $this->ssh->execute($commands);
	}

	public function download($remotePath, $localPath)
	{
		return $this->ssh->download($remotePath, $localPath);
	}
}

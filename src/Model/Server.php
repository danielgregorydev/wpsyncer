<?php

namespace WpSyncer\Model;

abstract class Server
{
	abstract public function execute($commands);

	abstract public function wp($command);

	public function uploadPath()
	{
		return $this->wp('option get upload_path');
	}
}

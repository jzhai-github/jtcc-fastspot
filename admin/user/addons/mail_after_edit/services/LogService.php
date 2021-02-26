<?php

namespace TNS\MailAfterEdit;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LogService {

	public static function log($data)
	{
		$dateTime = date("Y-m-d H:i:s");
		ee()->load->library('logger');
		$stringToWrite = "[{$dateTime}]: " . json_encode($data);
		ee()->logger->developer($stringToWrite);
	}

}
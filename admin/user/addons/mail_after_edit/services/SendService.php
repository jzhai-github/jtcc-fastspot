<?php

namespace TNS\MailAfterEdit;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use TNS\MailAfterEdit\LogService;

class SendService {

	public static function send($outputEmails, $message, $subject, $from, $forceBcc = true)
	{

		ee()->load->library('email');

		ee()->load->helper('text');

		ee()->email->wordwrap = true;
		
		ee()->email->mailtype = 'html';
		
		ee()->email->from($from);
		
		ee()->email->to($forceBcc ? $from : $outputEmails);

		if($forceBcc) {

			ee()->email->bcc($outputEmails);

		}
		
		ee()->email->subject($subject);
		
		ee()->email->message(entities_to_ascii($message));
		
		$result = ee()->email->send();

		LogService::log('Sending from ' . $from);

		LogService::log('Email sent to ' . $outputEmails);

		LogService::log(
			$result
			? 'Email sent successfully.'
			: 'Error in sending.'
		);

		if(!$result) {
			LogService::log(ee()->email->print_debugger());
		}

		ee()->email->clear();

		return true;

	}

}
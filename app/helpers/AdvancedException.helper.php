<?php

/*
* This software is copyright protected. Use only allowed on licensed
* websites. Contact author for further information or to receive a license.
*
* @link http://marcoraddatz.com
* @copyright 2007 - 2008 Marco Raddatz
* @author Marco Raddatz <mr at marcoraddatz dot com>
* @package CMS
* @version 1.0
*/

# TODO DONT LOAD TWO TIMES
#require_once 'app/controllers/Mail.controller.php';

class AdvancedException extends Exception
{
	public function __contruct($sMessage, $iCode)
	{
		$sMessage = !empty($sMessage) ? $sMessage : $this->getMessage();
		$iCode		= !empty($iCode) ? $iCode : $this->getCode();

		$this->_sendAdminMail($sMessage, $iCode);
	}

	private function _sendAdminMail($sMessage, $iCode)
	{
		if(WEBSITE_DEV == false)
			Mail::send(WEBSITE_MAIL, 'Exception', $sMessage, false);
		else
			die($sMessage. ' -> '	.$iCode);
	}
}
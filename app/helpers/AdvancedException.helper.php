<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
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
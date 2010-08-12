<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class AdvancedException extends Exception
{
	public function __contruct($sMessage, $iCode)
	{
		$sMessage = !empty($sMessage) ? $sMessage : $this->getMessage();
		$iCode		= !empty($iCode) ? $iCode : $this->getCode();

		$this->_sendAdminMail($sMessage);
	}

	private function _sendAdminMail($sMessage)
	{
    if(!class_exists('Mail'))
      require_once 'app/controllers/Mail.controller.php';

    Mail::send(WEBSITE_MAIL, 'Exception', $sMessage, false);
	}
}
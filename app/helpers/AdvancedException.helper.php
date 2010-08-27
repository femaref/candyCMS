<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class AdvancedException extends ErrorException {

  public function __contruct($sMessage, $iCode) {
    $sMessage = !empty($sMessage) ? $sMessage : $this->getMessage();
    $iCode = !empty($iCode) ? $iCode : $this->getCode();

		$this->sendAdminMail($sMessage);
  }

  public function sendAdminMail($sMessage, $sSubject = 'Exception') {
    if (!class_exists('Mail'))
      require_once 'app/controllers/Mail.controller.php';

    Mail::send(WEBSITE_MAIL, $sSubject, $sMessage, false);
  }
}
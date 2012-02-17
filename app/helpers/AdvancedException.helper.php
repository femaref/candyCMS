<?php

/**
 * Show modified exceptions.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Helper;

use CandyCMS\Controller\Mail as Mail;

class AdvancedException extends \Exception {

  /**
	 * Report errors to our log and send a mail to the admin.
	 *
	 * @static
	 * @access public
   * @param type $sMessage
	 *
   */
  public static function reportBoth($sMessage) {
		AdvancedException::sendAdminMail($sMessage);
		AdvancedException::writeLog($sMessage);
	}

  /**
   * Send an email to an administrator when an error occurs.
   *
   * @static
   * @access public
   * @return boolean mail status
   *
   */
  public static function sendAdminMail($sMessage) {
		if (WEBSITE_MODE == 'production') {
			if (!class_exists('\CandyCMS\Controller\Mail'))
				require_once 'app/controllers/Mail.controller.php';

			$sMessage = date('Y-m-d Hi', time()) . ' - ' . $sMessage;
			return Mail::send(WEBSITE_MAIL, 'Exception', $sMessage, WEBSITE_MAIL_NOREPLY);
		}
	}

  /**
	 * Write an error message to own log.
	 *
	 * @static
	 * @access public
	 *
	 */
	public static function writeLog($sMessage) {
		$sMessage = date('Y-m-d Hi', time()) . ' - ' . $sMessage;

		$sFileName = PATH_STANDARD . '/logs/error.log';
		$oFile = fopen($sFileName, 'a');
		fputs($oFile, $sMessage . "\n");
		fclose($oFile);
	}
}
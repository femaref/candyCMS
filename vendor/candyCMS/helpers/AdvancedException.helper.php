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

namespace CandyCMS\Core\Helper;

use CandyCMS\Core\Controller\Mails as Mails;

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
    AdvancedException::writeLog($sMessage);

    if (WEBSITE_MODE == 'production' || WEBSITE_MODE == 'staging')
      AdvancedException::sendAdminMail($sMessage);
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
    if (!class_exists('\CandyCMS\Core\Controller\Mail'))
      require_once PATH_STANDARD . '/vendor/candyCMS/controllers/Mails.controller.php';

    $sMessage = date('Y-m-d Hi', time()) . ' - ' . $sMessage;
    return Mails::send(WEBSITE_MAIL, 'Exception', $sMessage, WEBSITE_MAIL_NOREPLY);
  }

  /**
	 * Write down an error message to own log.
	 *
	 * @static
	 * @access public
	 *
	 */
	public static function writeLog($sMessage) {
		$sMessage = date('Y-m-d Hi', time()) . ' - ' . $sMessage;

		$sFileName = PATH_STANDARD . '/app/logs/' . WEBSITE_MODE . '.log';
		$oFile = fopen($sFileName, 'a');
		fputs($oFile, $sMessage . "\n");
		fclose($oFile);
	}
}
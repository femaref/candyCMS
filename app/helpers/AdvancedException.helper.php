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

class AdvancedException extends \ErrorException {

  /**
   * Send an email to an administrator when an error occurs.
   *
   * @access public
   * @return boolean mail status
   *
   */
  public function sendAdminMail() {
    if (!class_exists('Mail'))
      require_once 'app/controllers/Mail.controller.php';

    return Mail::send(WEBSITE_MAIL, 'Exception', $this->getMessage(), false);
  }
}
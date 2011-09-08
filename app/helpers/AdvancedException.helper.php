<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

namespace CandyCMS\Helper;

class AdvancedException extends \ErrorException {

  public function sendAdminMail() {
    if (!class_exists('Mail'))
      require_once 'app/controllers/Mail.controller.php';

    return \CandyCMS\Controller\Mail::send(WEBSITE_MAIL, 'Exception', $this->getMessage(), false);
  }
}
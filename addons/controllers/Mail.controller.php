<?php

/**
 * This is an example for extending a standard class.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

require_once 'app/controllers/Mail.controller.php';

class Addon_Mail extends Mail {

  public function methodToOverride() {
    return 'This is an example!';
  }
}
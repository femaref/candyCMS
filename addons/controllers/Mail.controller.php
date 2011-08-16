<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/controllers/Mail.controller.php';

# This is an example for extending a standard class.
class Addon_Mail extends Mail {

  public function create() {
    return 'This is an example!';
  }
}
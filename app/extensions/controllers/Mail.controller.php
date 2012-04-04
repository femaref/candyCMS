<?php

/**
 * This is an example for extending a standard class.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Controllers;

use CandyCMS\core\Helpers\Helper;

require_once 'app/controllers/Mail.controller.php';

class ExtensionMail extends \CandyCMS\Core\Controller\Mail {

  public function methodToOverride() {
    return 'This is an example!';
  }
}
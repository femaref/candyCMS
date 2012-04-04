<?php

/**
 * This is an example for extending a standard class.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Controllers;

use CandyCMS\Core\Helpers\Helper;

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Mails.controller.php';

class Mails extends \CandyCMS\Core\Controllers\Mails {

  /**
   * This method overrides the standard update method and is used for tests.
   *
   * @access public
   * @return string
   * @todo test
   *
   */
  public function update() {
    return 'This is an example!';
  }
}
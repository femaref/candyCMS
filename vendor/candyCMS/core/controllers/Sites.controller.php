<?php

/**
 * Show a static page.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Core\Controllers;

use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\I18n;

class Sites extends Main {

  /**
   * Print out a static page.
   *
   * An example would be an URL linking to "/sites/welcome" when there is a template named
	 * "welcome.tpl" in the static folder defined in the "app/config/Candy.inc.php" (PATH_STATIC -
   * normally located at "/public/_static/").
	 *
	 * @access protected
	 * @return string HTML content
   *
   */
  protected function _show() {
    $sSite = isset($this->_aRequest['site']) ? (string) $this->_aRequest['site'] : '';

    if (!file_exists(PATH_STATIC_TEMPLATES . '/' . $sSite . '.tpl'))
      Helper::redirectTo('/errors/404');

    $this->setTitle(ucfirst($sSite));
    return $this->oSmarty->fetch(PATH_STATIC_TEMPLATES . '/' . $sSite . '.tpl');
  }
}
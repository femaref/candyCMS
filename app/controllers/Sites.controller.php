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

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use Smarty;

class Sites extends Main {

  /**
   * Print out a static page. An example would be "/site/welcome" when there is a template named
	 * "welcome.tpl" in the static folder defined in the "config/Candy.inc.php" (PATH_STATIC).
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
    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $this->oSmarty->setCacheLifetime(300);
    return $this->oSmarty->fetch(PATH_STATIC_TEMPLATES . '/' . $sSite . '.tpl');
  }
}
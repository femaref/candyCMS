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

class Site extends Main {

  /**
   *
   */
  protected function _show() {
    $sSite = isset($this->_aRequest['site']) ?(string) $this->_aRequest['site'] : '';

    if (!file_exists(PATH_STATIC_TEMPLATES . '/' . $sSite . '.tpl')) {
      Helper::redirectTo('/error/404');
      exit();
    }

    $this->setDescription(ucfirst($sSite));
    $this->setTitle(ucfirst($sSite));

    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $this->oSmarty->setCacheLifetime(300);

    return $this->oSmarty->fetch(PATH_STATIC_TEMPLATES . '/' . $sSite . '.tpl');
  }
}
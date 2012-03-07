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
  public function show() {
    $sTpl = isset($this->_aRequest['subsection']) ?
            (string) $this->_aRequest['subsection'] :
            '';

    if (!file_exists(PATH_STATIC_TEMPLATES . '/' . $sTpl . '.tpl')) {
      Helper::redirectTo('/error/404');
      exit();
    }

    $this->setDescription(ucfirst($sTpl));
    $this->setTitle(ucfirst($sTpl));

    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $this->oSmarty->setCacheLifetime(300);

    return $this->oSmarty->fetch(PATH_STATIC_TEMPLATES . '/' . $sTpl . '.tpl');
  }
}
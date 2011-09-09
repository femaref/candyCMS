<?php

/**
 * Show customized error message when page is not found.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;

require_once 'app/controllers/Search.controller.php';

class Error extends Main {

  /**
   * Show a 404 error when a page is not avaiable or found.
   *
   * @access public
   * @return string HTML content
   * @todo Some language stuff.
   *
   */
  public function show404() {
    $this->_oSmarty->assign('lang_headline', LANG_ERROR_GLOBAL_404_TITLE);
    $this->_oSmarty->assign('lang_info', LANG_ERROR_GLOBAL_404_INFO);
    $this->_oSmarty->assign('lang_subheadline', 'Meinten Sie vielleicht:');

    if (isset($this->_aRequest['seo_title'])) {
      $oSearch = new Search($this->_aRequest, $this->_aSession);
      $oSearch->__init();
      $this->_oSmarty->assign('_search_', $oSearch->getSearch(urldecode($this->_aRequest['seo_title'])));
    }

    $this->_oSmarty->template_dir = Helper::getTemplateDir('errors', '404');
    return $this->_oSmarty->fetch('404.tpl');
  }
}
<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Error extends Main {

  public function show404() {
    $this->_oSmarty->assign('lang_headline', LANG_ERROR_GLOBAL_404_TITLE);
    $this->_oSmarty->assign('lang_info', LANG_ERROR_GLOBAL_404_INFO);
    
    $this->_oSmarty->assign('_sitemap_', $this->_getSitemap());
    
    $this->_oSmarty->template_dir = Helper::getTemplateDir('errors/404');
    return $this->_oSmarty->fetch('errors/404.tpl');
  }
}
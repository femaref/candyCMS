<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Search.controller.php';

class Error extends Main {
	# Empty, but required from section helper

	public function __init() {}

	public function show404() {
		$this->_oSmarty->assign('lang_headline', LANG_ERROR_GLOBAL_404_TITLE);
		$this->_oSmarty->assign('lang_info', LANG_ERROR_GLOBAL_404_INFO);
		$this->_oSmarty->assign('lang_subheadline', 'Meinten Sie vielleicht:');

		if (isset($this->_aRequest['seo_title'])) {
      $oSearch = new Search($this->_aRequest, $this->_aSession);
      $oSearch->__init();
      $this->_oSmarty->assign('_search_', $oSearch->getSearch(urldecode($this->_aRequest['seo_title'])));
    }
            
		$this->_oSmarty->template_dir = Helper::getTemplateDir('errors/404');
		return $this->_oSmarty->fetch('errors/404.tpl');
	}
}
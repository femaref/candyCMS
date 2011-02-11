<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Log.model.php';
require_once 'app/helpers/Page.helper.php';

class Log extends Main {
  public function __init() {
    $this->_oModel = new Model_Log($this->_aRequest, $this->_aSession);
  }

	public function overview() {
		if( USER_RIGHT < 4 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
		else {
			$this->_oSmarty->assign('logs', $this->_oModel->getData());
			$this->_oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
			$this->_oSmarty->assign('lang_headline', LANG_GLOBAL_LOGS);

			$this->_oSmarty->template_dir = Helper::getTemplateDir('logs/overview');
			return $this->_oSmarty->fetch('logs/overview.tpl');
		}
	}
}
<?php

/*
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
		if (USER_RIGHT < 4)
			return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');
		else {
			$this->_oSmarty->assign('logs', $this->_oModel->getData());

      # Do we need pages?
      $this->_oSmarty->assign('_pages_', $this->_oModel->oPage->showPages('/log'));

			$this->_oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
			$this->_oSmarty->assign('lang_headline', LANG_GLOBAL_LOGS);

			$this->_oSmarty->template_dir = Helper::getTemplateDir('logs/overview');
			return $this->_oSmarty->fetch('logs/overview.tpl');
		}
	}

	# Insert log entry into database
	public static function insert($sSectionName, $sActionName, $iActionId = 0, $iUserId = USER_ID, $iTimeStart = '', $iTimeEnd = '') {
		Model_Log::insert($sSectionName, $sActionName, $iActionId, $iUserId, $iTimeStart, $iTimeEnd);
	}

	# @Override
	public function destroy() {
		if (USER_RIGHT < 4)
			return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');
		else
			return $this->_destroy();
	}

	protected function _destroy() {
		$sRedirect = '/log';

		if ($this->_oModel->destroy($this->_iId) === true) {
			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $this->_iId);
			return Helper::successMessage(LANG_SUCCESS_DESTROY, $sRedirect);
		}
		else {
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
			unset($this->_iId);
		}
	}
}
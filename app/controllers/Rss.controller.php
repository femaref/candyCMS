<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Blog.model.php';
require_once 'app/helpers/Pages.helper.php';

class Rss {
	private $_sAction;
	private $_oModel;
	private $_iLimit;
	private $_aRequest;
	private $_aSession;

	public function __construct($aRequest, $aSession) {
		$this->_aRequest =& $aRequest;
		$this->_aSession =& $aSession;
	}

	public function __init() {
		$this->_sAction = isset( $this->_aRequest['action'] ) ?
				(string)$this->_aRequest['action'] :
				'blog';

		$this->_iLimit = LIMIT_BLOG;
		$this->_oModel = new Model_Blog($this->_aRequest, $this->_aSession);
	}

	public function show() {
		$oSmarty = new Smarty();
		$oSmarty->assign('data', $this->_oModel->getData());
		$oSmarty->assign('title', LANG_WEBSITE_TITLE);
		$oSmarty->assign('description', '');
		$oSmarty->assign('link', WEBSITE_URL);
		$oSmarty->assign('copyright', date('Y') );
		$oSmarty->assign('action', $this->_sAction );

		# Language
		$oSmarty->assign('date', LANG_GLOBAL_DATE );
		$oSmarty->assign('location', LANG_GLOBAL_LOCATION );

		$oSmarty->template_dir = Helper::getTemplateDir('rss/show');
		return $oSmarty->fetch('rss/show.tpl');
	}
}
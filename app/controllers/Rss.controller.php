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
	private $m_aRequest;
	private $m_oSession;

	public function __construct($aRequest, $oSession) {
		$this->m_aRequest =& $aRequest;
		$this->m_oSession =& $oSession;
	}

	public function __init() {
		$this->_sAction = isset( $this->m_aRequest['action'] ) ?
				(string)$this->m_aRequest['action'] :
				'overview';

		$this->_iLimit = LIMIT_BLOG;
		$this->_oModel = new Model_Blog($this->m_aRequest, $this->m_oSession);
	}

	public function show() {
		$oSmarty = new Smarty();
		$oSmarty->assign('data', $this->_oModel->getData());
		$oSmarty->assign('title', WEBSITE_TITLE);
		$oSmarty->assign('description', '');
		$oSmarty->assign('link', WEBSITE_URL);
		$oSmarty->assign('copyright', date('Y') );
		$oSmarty->assign('action', $this->_sAction );

		# Language
		$oSmarty->assign('date', LANG_GLOBAL_DATE );
		$oSmarty->assign('location', LANG_GLOBAL_LOCATION );

		$oSmarty->template_dir = Helper::templateDir('rss/show');
		return $oSmarty->fetch('rss/show.tpl');
	}
}
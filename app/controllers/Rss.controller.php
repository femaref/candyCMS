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
		$this->_aRequest = & $aRequest;
		$this->_aSession = & $aSession;
	}

	public function __init() {
		$this->_sSection = isset($this->_aRequest['template']) ?
						(string) ucfirst($this->_aRequest['template']) :
						'Blog';

		if($this->_sSection == 'Blog')
			$this->_oModel = new Model_Blog($this->_aRequest, $this->_aSession);
	}

	public function show() {
		$oSmarty = new Smarty();
		$oSmarty->assign('_copyright_', date('Y'));
		$oSmarty->assign('_description_', '');
		$oSmarty->assign('_language_', strtolower(DEFAULT_LANGUAGE));
		$oSmarty->assign('_section_', $this->_sSection);

		$oSmarty->assign('WEBSITE_URL', WEBSITE_URL);
		$oSmarty->assign('data', $this->_oModel->getData());

		# Language
		$oSmarty->assign('lang_website_title', LANG_WEBSITE_TITLE);

		$oSmarty->cache_dir = CACHE_DIR;
		$oSmarty->compile_dir = COMPILE_DIR;
		$oSmarty->template_dir = Helper::getTemplateDir('rss/show');
		return $oSmarty->fetch('rss/show.tpl');
	}
}
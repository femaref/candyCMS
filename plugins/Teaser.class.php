<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/controllers/Blog.controller.php';
require_once 'app/models/Blog.model.php';

# Show the last blog entry with teaser text
class Teaser extends Blog {

	public function __construct($aRequest, $aSession) {
		$this->_aRequest = & $aRequest;
		$this->_aSession = & $aSession;
		$this->__init();
	}

	public function __init() {
		$this->_oModel = new Model_Blog($this->_aRequest, $this->_aSession);
	}

	public function show() {
		$this->_aData = $this->_oModel->getData('', false, 1);

    $oSmarty = new Smarty();
		$oSmarty->cache_dir = CACHE_DIR;
		$oSmarty->compile_dir = COMPILE_DIR;

    $oSmarty->assign('data', $this->_aData);
    $oSmarty->template_dir = 'public/skins/_plugins/teaser';
		return $oSmarty->fetch('show.tpl');
	}
}
<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

abstract class Model_Main {
	protected $_aRequest;
	protected $_aSession;
	protected $_aData;
	protected $_iId;
	public $oPages;

	public function __construct($aRequest = '', $aSession = '', $aFile = '') {
		$this->_aRequest	=& $aRequest;
		$this->_aSession	=& $aSession;
		$this->_aFile			=& $aFile;
	}

	public function create() {

  }

  protected function update() {

  }

  public function destroy() {

  }
}
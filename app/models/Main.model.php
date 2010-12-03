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
  protected $_oDb;
  public $oPages;

  public function __construct($aRequest = '', $aSession = '', $aFile = '') {
    $this->_aRequest = & $aRequest;
    $this->_aSession = & $aSession;
    $this->_aFile = & $aFile;

    $this->_oDb = new PDO('mysql:host=' . SQL_HOST . ';port=' . SQL_PORT . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
                PDO::ATTR_PERSISTENT => true));
    $this->_oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function create() {

  }

  protected function update() {

  }

  public function destroy() {

  }
}
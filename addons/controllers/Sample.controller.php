<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

#require_once 'addons/models/Sample.model.php';

# This is an example for a single addon. Properties were set in Addon.helper.php
class Addon_Sample extends Main {

  public function __construct($aRequest, $aSession, $aFile = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;
  }

  public function __init() {
    $this->_oModel = new Model_Download($this->_aRequest, $this->_aSession, $this->_aFile);
  }

  public function show() {
    return 'Example';
  }

  protected final function _showFormTemplate($bUpdate = true) {

  }

  protected final function _create() {

  }

  protected final function _update() {

  }

  protected final function _destroy() {

  }
}
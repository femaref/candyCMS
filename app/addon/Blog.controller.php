<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

/* This class could override the existing blog class easily. Perfect for project specific functions. */

require_once 'app/models/Blog.model.php';

class Extension_Blog {

  public function __construct($aRequest, $aSession, $aFile = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;
  }

  public function __init() {
    $this->_oModel = new Model_Blog($this->_aRequest, $this->_aSession);
  }

  public function show() {

  }
}
?>

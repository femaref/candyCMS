<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# This is an example for a single addon. Properties were set in Addon.helper.php
class Addon_Projects {

  public function __construct($aRequest, $aSession, $aFile = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;
  }

  public function __init() {
    # Your init functions
  }

  public function show() {
    return 'This is an example!';
  }
}
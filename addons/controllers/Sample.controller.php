<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @todo put to PHP5
 */

#require_once 'addons/models/Sample.model.php';
# This is an example for a single addon. Properties were set in Addon.helper.php

namespace CandyCMS\Addon\Controller;

use CandyCMS\Helper\Helper as Helper;

class Addon_Sample extends \CandyCMS\Controller\Main {

  public function __construct($aRequest, $aSession, $aFile = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;
  }

  public function __init() {

  }

  public function show() {
    return 'Example';
  }

  protected function _showFormTemplate($bUpdate = true) {

  }

  protected function _create() {

  }

  protected function _update() {

  }

  protected function _destroy() {

  }
}
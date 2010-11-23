<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

if (!class_exists('Section'))
  require_once 'app/helpers/Section.helper.php';

final class Addon extends Section {

  public final function __construct($aRequest, $aSession, $aFile = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;

    $this->_setModules();
    $this->_getSection();
  }

  private final function _setModules() {
    $oDir = opendir('app/addons');

    while ($aFile = readdir($oDir)) {
      if ($aFile == '.' || $aFile == '..' || $aFile == '_dev' || $aFile == '.htaccess')
        continue;

      require_once ('app/addons/' . $aFile);
    }
  }

  private final function _getSection() {
    switch (strtolower($this->_aRequest['section'])) {
      default:
      case '404':

        parent::_setContent(Helper::errorMessage(LANG_ERROR_GLOBAL_404));
        parent::_setTitle(LANG_ERROR_GLOBAL_404);

        break;

      # Enter your addon information here
    }
  }
}
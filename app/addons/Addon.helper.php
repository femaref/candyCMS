<?php

/*
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
  }

  public final function setModules() {
    $oDir = opendir('app/addons');

    while ($aFile = readdir($oDir)) {
      if ($aFile == '.' || $aFile == '..' || $aFile == '_dev' || $aFile == '.htaccess')
        continue;

      require_once ('app/addons/' . $aFile);
    }
  }

  public final function getSection() {
    switch (strtolower($this->_aRequest['section'])) {
      default:
      case '404':

        # There is no such requested addon
        header('Status: 404 Not Found');
        Helper::redirectTo('/error/404/' . $this->_aRequest['section']);

        break;

      # This is a sample addon to display your projects and can be removed
      case 'projects':

        $oProjects = new Addon_Projects($this->_aRequest, $this->_aSession, $this->_aFile);

        parent::_setContent($oProjects->show());
        parent::_setDescription('My description');
        parent::_setKeywords('Keyword,Keyword,Keyword');
        parent::_setTitle('My new title');

        break;

      # Enter your addons here
    }
  }
}
<?php

/*
 * This software is copyright protected. Use only allowed on licensed
 * websites. Contact author for further information or to receive a license.
 *
 * @link http://marcoraddatz.com
 * @copyright 2007 - 2008 Marco Raddatz
 * @author Marco Raddatz <mr at marcoraddatz dot com>
 * @package CMS
 * @version 1.0
*/

abstract class Main {
  protected $m_aRequest;
  protected $m_oSession;
  protected $m_aFile;
  protected $_iID;
  private $_aData = array();
  private $_sContent;
  private $_sTitle;
  private $_oModel;

  public function __construct($aRequest, $oSession, $aFile = '') {
    $this->m_aRequest	=& $aRequest;
    $this->m_oSession	=& $oSession;
    $this->m_aFile		=& $aFile;

    $this->_iID = isset($this->m_aRequest['id']) ?
                  (int)$this->m_aRequest['id'] :
                  '';
  }

  public function __autoload($sClass) {
    require_once('app/controllers/'	.(string)ucfirst($sClass).	'.controller.php');
  }

  /* Manage Page Title */
  protected function _setTitle($sTitle) {
    $this->_sTitle =& $sTitle;
  }

  public function getTitle() {
    if( $this->_sTitle !== '' )
      return $this->_sTitle;
    else
      return '';
  }

  /* Manage Page Content */
  protected function _setContent($sContent) {
    $this->_sContent =& $sContent;
  }

  public function getContent() {
    return $this->_sContent;
  }

  public function search() {
    return $this->show();
  }

  public function show() {
    $this->show();
  }

  public function create($sInputName) {
    if( USERRIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if( isset($this->m_aRequest[$sInputName]) )
        return $this->_create();
      else
        return $this->_showFormTemplate(false);
    }
  }

  public function update($sInputName) {
    if( USERRIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if( isset($this->m_aRequest[$sInputName]) )
        return $this->_update();
      else
        return $this->_showFormTemplate(true);
    }
  }

  public function destroy() {
    if( USERRIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else
      return $this->_destroy();
  }
}
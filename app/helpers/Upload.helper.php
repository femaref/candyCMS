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

final class Upload {
  private $_iID;
  private $_sUploadFolder;
  private $_sFormAction;
  private $m_aFile;
  private $_sRename;
  private $_sFileExtension = '';
  private $_sFinalPath;

  public final function __construct($aRequest, $aFile, $sRename = '') {
    $this->m_aRequest =& $aRequest;
    $this->m_aFile =& $aFile;
    $this->_sRename =& $sRename;
  }

  public function uploadMediaFile() {
    $this->_sFileExtension = strtolower(substr(strrchr($this->m_aFile['file']['name'], '.'), 1) );

    if($this->_sRename == '')
      $this->_iID = time().	'_'	.rand(100,999);
    else
      $this->_iID =& $this->_replaceNonAlphachars($this->_sRename);

    $this->_sFormAction = 'Media/upload';
    $this->_sUploadFolder = 'media';

    $this->_sFinalPath = PATH_UPLOAD.	'/'	.$this->_sUploadFolder.	'/'	.$this->_iID.
            '.'	.$this->_sFileExtension;

    move_uploaded_file(	$this->m_aFile['file']['tmp_name'], $this->_sFinalPath);
    return $this->_sFinalPath;
  }

  public function uploadGalleryFile($sResize = '') {
    $this->m_aRequest['cut'] = !empty($sResize) ? $sResize : $this->m_aRequest['cut'];

    $this->_sFileExtension = strtolower(substr(strrchr($this->m_aFile['Filedata']['name'], '.'), 1) );
    $this->_iID = $this->_replaceNonAlphachars($this->m_aFile['Filedata']['name']);
    $this->_iID = substr($this->_iID, 0, strlen($this->_iID) - strlen($this->_sFileExtension) - 1).rand(100,999);
    $this->_sUploadFolder = 'gallery/'	.(int)$this->m_aRequest['id'];
    $this->_sFinalPath = PATH_UPLOAD.	'/'	.$this->_sUploadFolder.	'/original/'	.$this->_iID.	'.' .$this->_sFileExtension;

    move_uploaded_file(	$this->m_aFile['Filedata']['tmp_name'], $this->_sFinalPath);

    $oImage = new Image($this->_iID, $this->_sUploadFolder, $this->_sFinalPath, $this->_sFileExtension);
    if(isset($this->m_aRequest['cut']) && 'c' == $this->m_aRequest['cut'])
      $oImage->resizeAndCut(THUMB_DEFAULT_X);

    elseif(isset($this->m_aRequest['cut']) && 'r' == $this->m_aRequest['cut'])
      $oImage->resizeDefault(THUMB_DEFAULT_X, THUMB_DEFAULT_X); #MAX Y?

    else
      throw new Exception('No resizing information!');

    $oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y);
    $oImage->resizeAndCut('32');

    return $this->_sFinalPath;
  }

  public function uploadAvatarFile($bReturnPath = true) {
    $this->_sFileExtension = strtolower(substr(strrchr($this->m_aFile['image']['name'], '.'), 1) );
    $this->_iID = USERID;
    $this->_sFormAction = 'User/Settings';
    $this->_sUploadFolder = 'user';
    $this->_sFinalPath = PATH_UPLOAD.	'/'	.$this->_sUploadFolder.	'/original/'	.$this->_iID.
            '.'	.$this->_sFileExtension;

    if($this->_iID == '0')
      return Helper::errorMessage(LANG_ERROR_GLOBAL_WRONG_ID);

    elseif( $this->m_aFile['image']['size'] > 409600) {
      $oSmarty = new Smarty();
      $oSmarty->assign('action', $this->_sFormAction );
      return Helper::errorMessage(LANG_ERROR_MEDIA_MAX_FILESIZE_REACHED);
    }
    elseif( $this->m_aFile['image']['type'] !== 'image/jpeg') {
      $oSmarty = new Smarty();
      return Helper::errorMessage(LANG_ERROR_MEDIA_WRONG_FILETYPE);
    }
    else {
      move_uploaded_file(	$this->m_aFile['image']['tmp_name'], $this->_sFinalPath);

      $oImage = new Image($this->_iID, $this->_sUploadFolder, $this->_sFinalPath, $this->_sFileExtension);

      $oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y);
      $oImage->resizeDefault(THUMB_DEFAULT_X);
      $oImage->resizeDefault('100');
      $oImage->resizeAndCut('64');
      $oImage->resizeAndCut('32');
      $oImage->resizeAndCut('18');

      if($bReturnPath == true)
        return $this->_sFinalPath;
    }
  }

  private function _replaceNonAlphachars($sStr) {
    $sStr = str_replace('"', '', $sStr);
    $sStr = str_replace('ä', 'ae', $sStr);
    $sStr = str_replace('ü', 'ue', $sStr);
    $sStr = str_replace('ö', 'oe', $sStr);
    $sStr = str_replace('ß', 'ss', $sStr);
    $sStr = str_replace(' ', '_', $sStr);
    $sStr = strtolower($sStr);
    return $sStr;
  }

  public function getExtension() {
    return $this->_sFileExtension;
  }

  public function getId() {
    return $this->_iID.	'.'	.$this->_sFileExtension;
  }

  # SIMPLE FILE UPLOAD
  public final function uploadFile() {
    move_uploaded_file($this->m_aFile['file']['tmp_name'], $this->_sFinalPath);
    return $this->_sFinalPath;
  }

  public final function uploadMessage($sFile) {
    if( file_exists($sFile) )
      return Helper::successMessage(str_replace('%p',
        WEBSITE_URL.	'/'	.$sFile, LANG_MEDIA_FILE_UPLOAD_SUCCESS));

    else
      return Helper::errorMessage(LANG_ERROR_UPLOAD_FAILED);
  }
}
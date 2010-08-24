<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

final class Upload {
  private $_iId;
  private $_sUploadFolder;
  private $_sFormAction;
  private $_aFile;
  private $_sRename;
  private $_sFileExtension = '';
  private $_sFinalPath;

  public final function __construct($aRequest, $aFile, $sRename = '') {
    $this->_aRequest	=& $aRequest;
    $this->_aFile			=& $aFile;
    $this->_sRename		=& $sRename;
  }

  public function uploadMediaFile() {
    $this->_iId = $this->_replaceNonAlphachars(strtolower($this->_aFile['file']['name']));
    $this->_sFileExtension = strtolower(substr(strrchr($this->_aFile['file']['name'], '.'), 1) );

    $this->_sFormAction = 'Media/upload';
    $this->_sUploadFolder = 'media';

    $this->_sFinalPath = PATH_UPLOAD.	'/'	.$this->_sUploadFolder.	'/'	.$this->_iId;

    if(!empty($this->_sRename)) {
      $this->_iId =& $this->_replaceNonAlphachars($this->_sRename);
      $this->_sFinalPath = $this->sFinalPath. '.'	.$this->_sFileExtension;
    }

    move_uploaded_file(	$this->_aFile['file']['tmp_name'], $this->_sFinalPath);
    return $this->_sFinalPath;
  }

  public function uploadGalleryFile($sResize = '') {
    $this->_aRequest['cut'] = !empty($sResize) ? $sResize : $this->_aRequest['cut'];

    $this->_sFileExtension = strtolower(substr(strrchr($this->_aFile['Filedata']['name'], '.'), 1) );
    $this->_iId = $this->_replaceNonAlphachars($this->_aFile['Filedata']['name']);
    $this->_iId = substr($this->_iId, 0, strlen($this->_iId) - strlen($this->_sFileExtension) - 1).rand(100,999);
    $this->_sUploadFolder = 'gallery/'	.(int)$this->_aRequest['id'];
    $this->_sFinalPath = PATH_UPLOAD.	'/'	.$this->_sUploadFolder.	'/original/'	.$this->_iId.	'.' .$this->_sFileExtension;

    move_uploaded_file(	$this->_aFile['Filedata']['tmp_name'], $this->_sFinalPath);

    $oImage = new Image($this->_iId, $this->_sUploadFolder, $this->_sFinalPath, $this->_sFileExtension);
    if(isset($this->_aRequest['cut']) && 'c' == $this->_aRequest['cut'])
      $oImage->resizeAndCut(THUMB_DEFAULT_X);

    elseif(isset($this->_aRequest['cut']) && 'r' == $this->_aRequest['cut'])
      $oImage->resizeDefault(THUMB_DEFAULT_X, THUMB_DEFAULT_X); #MAX Y?

    else
      throw new Exception('No resizing information!');

    $oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y);
    $oImage->resizeAndCut('32');

    return $this->_sFinalPath;
  }

  public function uploadAvatarFile($bReturnPath = true) {
    $this->_sFileExtension = strtolower(substr(strrchr($this->_aFile['image']['name'], '.'), 1) );
    $this->_iId = USER_ID;
    $this->_sFormAction = 'User/Settings';
    $this->_sUploadFolder = 'user';
    $this->_sFinalPath = PATH_UPLOAD.	'/'	.$this->_sUploadFolder.	'/original/'	.$this->_iId.
            '.'	.$this->_sFileExtension;

    if($this->_iId == '0')
      return Helper::errorMessage(LANG_ERROR_GLOBAL_WRONG_ID);

    elseif( $this->_aFile['image']['size'] > 409600) {
      $oSmarty = new Smarty();
      $oSmarty->assign('action', $this->_sFormAction );
      return Helper::errorMessage(LANG_ERROR_MEDIA_MAX_FILESIZE_REACHED);
    }
    elseif( $this->_aFile['image']['type'] !== 'image/jpeg') {
      $oSmarty = new Smarty();
      return Helper::errorMessage(LANG_ERROR_MEDIA_WRONG_FILETYPE);
    }
    else {
      move_uploaded_file(	$this->_aFile['image']['tmp_name'], $this->_sFinalPath);

      $oImage = new Image($this->_iId, $this->_sUploadFolder, $this->_sFinalPath, $this->_sFileExtension);

      $oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y);
      $oImage->resizeDefault(THUMB_DEFAULT_X);
      $oImage->resizeDefault('100');
      $oImage->resizeAndCut('64');
      $oImage->resizeAndCut('32');

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

  /*public function getId() {
    return $this->_iId.	'.'	.$this->_sFileExtension;
  }*/

  # SIMPLE FILE UPLOAD
  public final function uploadFile() {
    move_uploaded_file($this->_aFile['file']['tmp_name'], $this->_sFinalPath);
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
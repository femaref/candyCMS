<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Upload {

  private $_aFile;
  private $_iId;
  private $_sFileExtension = '';
  private $_sRename;
  private $_sUploadFolder;
  public $sFilePath;

  public function __construct($aRequest, $aFile, $sRename = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aFile     = & $aFile;
    $this->_sRename   = & $sRename;
  }

  public function uploadFile($sFolder = 'media') {
    $this->_iId = $this->_replaceNonAlphachars(strtolower($this->_aFile['file']['name']));
    $this->_sFileExtension = strtolower(substr(strrchr($this->_aFile['file']['name'], '.'), 1));

    if (!empty($this->_sRename)) {
      $this->_sRename = & $this->_replaceNonAlphachars($this->_sRename);
      $this->_iId = $this->_sRename . '.' . $this->_sFileExtension;
    }

    $this->sFilePath = PATH_UPLOAD . '/' . $sFolder . '/' . $this->_iId;
    return move_uploaded_file($this->_aFile['file']['tmp_name'], $this->sFilePath);
  }

  public function uploadGalleryFile($sResize = '') {
    $this->_aRequest['cut'] = !empty($sResize) ? $sResize : $this->_aRequest['cut'];
    $this->_sFileExtension = strtolower(substr(strrchr($this->_aFile['name'], '.'), 1));
    $this->_iId = $this->_replaceNonAlphachars($this->_aFile['name']);
    $this->_iId = substr($this->_iId, 0, strlen($this->_iId) - strlen($this->_sFileExtension) - 1) . rand(100, 999);
    $this->_sUploadFolder = 'gallery/' . (int) $this->_aRequest['id'];
    $this->sFilePath = PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_iId . '.' . $this->_sFileExtension;

    $bReturn = move_uploaded_file($this->_aFile['tmp_name'], $this->sFilePath);

    $oImage = new Image($this->_iId, $this->_sUploadFolder, $this->sFilePath, $this->_sFileExtension);
    if (isset($this->_aRequest['cut']) && 'c' == $this->_aRequest['cut'])
      $oImage->resizeAndCut(THUMB_DEFAULT_X);

    elseif (isset($this->_aRequest['cut']) && 'r' == $this->_aRequest['cut'])
      $oImage->resizeDefault(THUMB_DEFAULT_X, THUMB_DEFAULT_X);

    else
      throw new Exception('No resizing information!');

    $oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y, 'popup');
    $oImage->resizeAndCut('32');

    return $bReturn;
  }

  public function uploadAvatarFile($bReturnPath = true) {
    $this->_sFileExtension = strtolower(substr(strrchr($this->_aFile['image']['name'], '.'), 1));
    $this->_iId = isset($this->_aRequest['id']) && USER_RIGHT == 4 ? (int)$this->_aRequest['id'] : USER_ID;
    $this->_sUploadFolder = 'user';
    $this->_sFilePath = PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_iId;

    if ($this->_iId == '0')
      return Helper::errorMessage(LANG_ERROR_GLOBAL_WRONG_ID);

    elseif ($this->_aFile['image']['size'] > 409600)
      return Helper::errorMessage(LANG_ERROR_MEDIA_MAX_FILESIZE_REACHED);

    else {

      $this->_deleteAvatarFiles();

      $bReturn = move_uploaded_file($this->_aFile['image']['tmp_name'], $this->_sFilePath . '.' . $this->_sFileExtension);

      $oImage = new Image($this->_iId, $this->_sUploadFolder, $this->_sFilePath . '.' . $this->_sFileExtension, $this->_sFileExtension);
      $oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y, 'popup');
      $oImage->resizeDefault(THUMB_DEFAULT_X);
      $oImage->resizeDefault('100');
      $oImage->resizeAndCut('64');

      if ($bReturnPath == true)
        return $this->_sFilePath . '.' . $this->_sFileExtension;
      else
        return $bReturn;
    }
  }

  private function _deleteAvatarFiles() {
    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_iId . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_iId . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_iId . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_iId . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_iId . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_iId . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_iId . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_iId . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_iId . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_iId . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_iId . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_iId . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_iId . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_iId . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_iId . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_iId . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_iId . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_iId . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_iId . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_iId . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_iId . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_iId . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_iId . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_iId . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_iId . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_iId . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_iId . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_iId . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_iId . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_iId . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_iId . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_iId . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_iId . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_iId . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_iId . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_iId . '.gif');
  }

  private function _replaceNonAlphachars($sStr) {
    $sStr = str_replace('"', '', $sStr);
    $sStr = str_replace('Ä', 'Ae', $sStr);
    $sStr = str_replace('ä', 'ae', $sStr);
    $sStr = str_replace('Ü', 'Ue', $sStr);
    $sStr = str_replace('ü', 'ue', $sStr);
    $sStr = str_replace('Ö', 'Oe', $sStr);
    $sStr = str_replace('ö', 'oe', $sStr);
    $sStr = str_replace('ß', 'ss', $sStr);
    $sStr = str_replace(' ', '_', $sStr);
    $sStr = strtolower($sStr);
    return $sStr;
  }

  public function getExtension() {
    return $this->_sFileExtension;
  }

  # Return the current file
  public function getId() {
    return $this->_iId;
  }
}
<?php

/**
 * Upload and show media files.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
*/

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Upload as Upload;

require_once 'app/helpers/Image.helper.php';
require_once 'app/helpers/Upload.helper.php';

class Media extends Main {

  /**
   * Upload media file.
   * We must override the main method due to a diffent required user right.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   * @override app/controllers/Main.controller.php
   *
   */
  public function create() {
    if (USER_RIGHT < 3)
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

    else {
      if (isset($this->_aRequest['create_file'])) {
        if ($this->_proceedUpload() == true)
          return Helper::successMessage(LANG_MEDIA_FILE_CREATE_SUCCESSFUL, '/media');
        else
          return Helper::errorMessage(LANG_ERROR_UPLOAD_CREATE, '/media');
      }
      else
        return $this->_showUploadFileTemplate();
    }
  }

  /**
   * Build form template to create an upload.
   *
   * @access private
   * @return string HTML content
   *
   */
  private function _showUploadFileTemplate() {
    # Language
    $this->_oSmarty->assign('lang_file_choose', LANG_MEDIA_FILE_CREATE_LABEL_CHOOSE);
    $this->_oSmarty->assign('lang_file_rename', LANG_MEDIA_FILE_CREATE_LABEL_RENAME_FILE);
    $this->_oSmarty->assign('lang_file_create_info', LANG_MEDIA_FILE_CREATE_INFO);
    $this->_oSmarty->assign('lang_headline', LANG_MEDIA_FILE_CREATE_TITLE);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('medias', 'create');
    return $this->_oSmarty->fetch('create.tpl');
  }

  /**
   * Upload file.
   *
   * @access private
   * @return boolean status of upload.
   *
   */
  private function _proceedUpload() {
    $oUpload = new Upload($this->_aRequest, $this->_aFile, $this->_aRequest['rename']);
    return $oUpload->uploadFile('media');
  }

  /**
   * Show media files overview.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function show() {
    if (USER_RIGHT < 3)
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

    else {
      $sOriginalPath = PATH_UPLOAD . '/media';
      $oDir = opendir($sOriginalPath);

      $aFiles = array();
      while ($sFile = readdir($oDir)) {
        $sPath = $sOriginalPath . '/' . $sFile;

        if (substr($sFile, 0, 1) == '.' || is_dir($sPath))
          continue;

        $sFileType = strtolower(substr(strrchr($sPath, '.'), 1));
        $iNameLen = strlen($sFile) - 4;

        if ($sFileType == 'jpeg')
          $iNameLen--;

        $sFileName = substr($sFile, 0, $iNameLen);

        if ($sFileType == 'jpg' || $sFileType == 'jpeg' || $sFileType == 'png' || $sFileType == 'gif') {
          $aImgDim = getImageSize($sPath);

          if (!file_exists(PATH_UPLOAD . '/temp/media/' . $sFile)) {
            $oImage = new \CandyCMS\Helper\Image($sFileName, 'temp', $sPath, $sFileType);
            $oImage->resizeAndCut('32', 'media');
          }
        }
        else
          $aImgDim = '';
          $aFiles[] = array(
              'name'  => $sFile,
              'cdate' => Helper::formatTimestamp(filectime($sPath), true),
              'size'  => Helper::getFileSize($sPath),
              'type'  => $sFileType,
              'dim'   => $aImgDim
        );
      }

      closedir($oDir);

      $this->_oSmarty->assign('files', $aFiles);

      # Language
      $this->_oSmarty->assign('lang_destroy', LANG_MEDIA_FILE_DESTROY_TITLE);
      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_FILEMANAGER);
      $this->_oSmarty->assign('lang_file_create', LANG_MEDIA_FILE_CREATE_TITLE);
      $this->_oSmarty->assign('lang_no_files', LANG_ERROR_MEDIA_FILE_EMPTY_FOLDER);
      $this->_oSmarty->template_dir = Helper::getTemplateDir('medias', 'show');
      return $this->_oSmarty->fetch('show.tpl');
    }
  }

  /**
   * Delete a file.
   *
   * @access public
   * @return boolean status of model action
   * @override app/controllers/Main.controller.php
   *
   */
  public function destroy() {
    if (USER_RIGHT < 3)
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

    else {
      if (is_file(PATH_UPLOAD . '/media/' . $this->_aRequest['id'])) {
        unlink(PATH_UPLOAD . '/media/' . $this->_aRequest['id']);
        return Helper::successMessage(LANG_MEDIA_FILE_DESTROY_SUCCESSFUL, '/media');
      }
      else
        return Helper::errorMessage(LANG_ERROR_MEDIA_FILE_NOT_AVAIABLE, '/media');
    }
  }
}
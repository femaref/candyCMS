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
use CandyCMS\Helper\Image as Image;
use CandyCMS\Helper\Upload as Upload;
use Smarty;

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
    if ($this->_aSession['userdata']['right'] < 3)
      return Helper::errorMessage($this->oI18n->get('error.missing.permission'), '/');

    else {
      if (isset($this->_aRequest['create_file'])) {
        if ($this->_proceedUpload() == true)
          return Helper::successMessage($this->oI18n->get('success.file.upload'), '/media');
        else
          return Helper::errorMessage($this->oI18n->get('error.file.upload'), '/media');
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
    $sTemplateDir = Helper::getTemplateDir('medias', 'create');
    $this->oSmarty->template_dir = $sTemplateDir;
    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'create'));
  }

  /**
   * Upload file.
   *
   * @access private
   * @return boolean status of upload.
   *
   */
  private function _proceedUpload() {
    $oUpload = new Upload($this->_aRequest, $this->_aSession, $this->_aFile, $this->_aRequest['rename']);
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
    if ($this->_aSession['userdata']['right'] < 3)
      return Helper::errorMessage($this->oI18n->get('error.missing.permission'), '/');

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
            $oImage = new Image($sFileName, 'temp', $sPath, $sFileType);
            $oImage->resizeAndCut('32', 'media');
          }
        }
        else
          $aImgDim = '';
          $aFiles[] = array(
              'name'  => $sFile,
              'cdate' => Helper::formatTimestamp(filectime($sPath), 1),
              'size'  => Helper::getFileSize($sPath),
              'type'  => $sFileType,
              'dim'   => $aImgDim
        );
      }

      closedir($oDir);

      $this->oSmarty->assign('files', $aFiles);

    $sTemplateDir = Helper::getTemplateDir('medias', 'show');
    $this->oSmarty->template_dir = $sTemplateDir;
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'show'));
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
    if ($this->_aSession['userdata']['right'] < 3)
      return Helper::errorMessage($this->oI18n->get('error.missing.permission'), '/');

    else {
      if (is_file(PATH_UPLOAD . '/media/' . $this->_aRequest['id'])) {
        unlink(PATH_UPLOAD . '/media/' . $this->_aRequest['id']);
        return Helper::successMessage($this->oI18n->get('success.file.destroy'), '/media');
      }
      else
        return Helper::errorMessage($this->oI18n->get('error.missing.file'), '/media');
    }
  }
}
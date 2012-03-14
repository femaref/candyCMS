<?php

/**
 * Upload and show media files.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
*/

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Helper\Image as Image;
use CandyCMS\Helper\Upload as Upload;
use Smarty;

class Medias extends Main {

  /**
   * Upload media file.
   * We must override the main method due to a file upload.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function create() {
    if ($this->_aSession['user']['role'] < 3)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

    else {
      if (isset($this->_aRequest['create_file']))
				return $this->_proceedUpload() === true ?
								Helper::successMessage(I18n::get('success.file.upload'), '/' . $this->_aRequest['controller']) :
								Helper::errorMessage(I18n::get('error.file.upload'), '/' . $this->_aRequest['controller']);

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
    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'create');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'create');

    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Upload file.
   *
   * @access private
   * @return boolean status of upload.
   *
   */
  private function _proceedUpload() {
    require PATH_STANDARD . '/app/helpers/Upload.helper.php';

    $oUpload = new Upload($this->_aRequest, $this->_aSession, $this->_aFile, $this->_aRequest['rename']);
    $sFolder = isset($this->_aRequest['folder']) ?
            Helper::formatInput($this->_aRequest['folder']) :
            $this->_aRequest['controller'];

    if (!is_dir($sFolder))
      mkdir(Helper::removeSlash(PATH_UPLOAD . '/' . $sFolder, 0777));

    return $oUpload->uploadFile($sFolder);
  }

  /**
   * Show media files overview.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   * @todo rename temp
   *
   */
  protected function _show() {
    if ($this->_aSession['user']['role'] < 3)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

    else {
			require PATH_STANDARD . '/app/helpers/Image.helper.php';

      $sOriginalPath = Helper::removeSlash(PATH_UPLOAD . '/' . $this->_aRequest['controller']);
      $oDir = opendir($sOriginalPath);

      $aFiles = array();
      while ($sFile = readdir($oDir)) {
        $sPath = $sOriginalPath . '/' . $sFile;

        if (substr($sFile, 0, 1) == '.' || is_dir($sPath))
          continue;

        $sFileType	= strtolower(substr(strrchr($sPath, '.'), 1));
        $iNameLen		= strlen($sFile) - 4;

        if ($sFileType == 'jpeg')
          $iNameLen--;

        $sFileName = substr($sFile, 0, $iNameLen);

        if ($sFileType == 'jpg' || $sFileType == 'jpeg' || $sFileType == 'png' || $sFileType == 'gif') {
          $aImgDim = getImageSize($sPath);

          if (!file_exists(PATH_UPLOAD . '/temp/' . $this->_aRequest['controller'] . '/' . $sFile)) {
            $oImage = new Image($sFileName, 'temp', $sPath, $sFileType);
            $oImage->resizeAndCut('32', $this->_aRequest['controller']);
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

      $this->setDescription(I18n::get('global.manager.media'));
      $this->setTitle(I18n::get('global.manager.media'));

      $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
  }

  /**
   * Delete a file.
   *
   * @access public
   * @return boolean status of model action
   *
   */
  public function destroy() {
    if ($this->_aSession['user']['role'] < 3)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

    else {
      $sPath = Helper::removeSlash(PATH_UPLOAD . '/' . $this->_aRequest['controller'] . '/' . $this->_aRequest['file']);

      if (is_file($sPath)) {
        unlink($sPath);
        return Helper::successMessage(I18n::get('success.file.destroy'), '/' . $this->_aRequest['controller']);
      }
      else
        return Helper::errorMessage(I18n::get('error.missing.file'), '/' . $this->_aRequest['controller']);
    }
  }
}
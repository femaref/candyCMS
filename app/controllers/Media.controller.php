<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/helpers/Image.helper.php';
require_once 'app/helpers/Upload.helper.php';

class Media extends Main {
  public function __init() {

  }

  # @Override
  # We need more / other methods than parent
  public function create() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if (isset($this->_aRequest['upload_file']))
        return $this->_proceedUpload();
      else
        return $this->_showUploadFileTemplate();
    }
  }

  private function _showUploadFileTemplate() {
    $oSmarty = new Smarty();

    /* Language */
    $oSmarty->assign('lang_file_choose', LANG_MEDIA_FILE_CHOOSE);
    $oSmarty->assign('lang_file_rename', LANG_MEDIA_FILE_RENAME);
    $oSmarty->assign('lang_file_create_info', LANG_MEDIA_FILE_CREATE_INFO);
    $oSmarty->assign('lang_headline', LANG_MEDIA_FILE_CREATE);

    $oSmarty->template_dir = Helper::getTemplateDir('media/create');
    return $oSmarty->fetch('media/create.tpl');
  }

  private function _proceedUpload() {
    $oUploadImage = new Upload($this->_aRequest, $this->_aFile, $this->_aRequest['rename']);
    $oUploadImage->uploadMediaFile();
    Header('Location:'	.WEBSITE_URL.	'/Media');
  }

  public function show() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);

    else {
      $sOriginalPath = PATH_UPLOAD.	'/media';
      $oDir = opendir($sOriginalPath);

      $aFiles = array();
      while($sFile = readdir($oDir)) {

        if(substr($sFile, 0, 1) == '.')
          continue;

        $sPath = $sOriginalPath.	'/'	.$sFile;
        $sFileType = strtolower(substr(strrchr($sPath, '.'), 1));
        $iNameLen = strlen($sFile) - 4;

        if( $sFileType == 'jpeg')
          $iNameLen--;

        $sFileName = substr($sFile, 0, $iNameLen);

        if(	$sFileType      == 'jpg' || $sFileType  == 'jpeg'|| $sFileType  == 'png' || $sFileType  == 'gif') {
          $aImgDim = getImageSize($sPath);

          if( ($sFileType == 'jpg' || $sFileType == 'jpeg' || $sFileType == 'gif' || $sFileType == 'png') &&
                  !is_file(PATH_UPLOAD.	'/temp/32/'	.$sFile)) {

            $oImage = new Image($sFileName, 'temp', $sPath, $sFileType);
            $oImage->resizeAndCut('32');
          }
        }
        else
          $aImgDim = '';
          $aFiles[] = array('name'  => $sFile,
                            'cdate' => Helper::formatTimestamp(filectime($sPath)),
                            'size'  => Helper::getFileSize($sPath),
                            'type'  => $sFileType,
                            'dim'   => $aImgDim
        );
      }

      closedir($oDir);

      $oSmarty = new Smarty();
      $oSmarty->assign('files', $aFiles);

      # Constants
      $oSmarty->assign('USER_RIGHT', USER_RIGHT);

      # System variables
      $oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '-min' : '');

      # Language
      $oSmarty->assign('lang_destroy', LANG_MEDIA_FILE_DESTROY);
      $oSmarty->assign('lang_headline', LANG_GLOBAL_FILEMANAGER);
      $oSmarty->assign('lang_file_create', LANG_MEDIA_FILE_CREATE);
      $oSmarty->assign('lang_no_files', LANG_MEDIA_FILE_EMPTY_FOLDER);

      $oSmarty->template_dir = Helper::getTemplateDir('media/show');
      return $oSmarty->fetch('media/show.tpl');
    }
  }

  public function destroy() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if(is_file(PATH_UPLOAD.	'/media/'	.$this->_aRequest['id'])) {
        unlink(PATH_UPLOAD.	'/media/'	.$this->_aRequest['id']);

        return Helper::successMessage(LANG_MEDIA_FILE_DESTROY_SUCCESS).
                Header('Location:'	.WEBSITE_URL.	'/Media');
      }
      else
        return Helper::errorMessage(LANG_ERROR_MEDIA_FILE_NOT_AVAIABLE);
    }
  }
}
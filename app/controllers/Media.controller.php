<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

require_once 'app/helpers/Image.helper.php';
require_once 'app/helpers/Upload.helper.php';

class Media extends Main {
  # @Override
  public function create() {
		if (USER_RIGHT < 3)
			return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

		else {
			if (isset($this->_aRequest['upload_file'])) {
				if ($this->_proceedUpload() == true)
					return Helper::successMessage(LANG_MEDIA_FILE_CREATE_SUCCESSFUL, '/media');
				else
					return Helper::errorMessage(LANG_ERROR_UPLOAD_CREATE, '/media');
			}
			else
				return $this->_showUploadFileTemplate();
		}
	}

  private function _showUploadFileTemplate() {
    # Language
    $this->_oSmarty->assign('lang_file_choose', LANG_MEDIA_FILE_CREATE_LABEL_CHOOSE);
    $this->_oSmarty->assign('lang_file_rename', LANG_MEDIA_FILE_CREATE_LABEL_RENAME_FILE);
    $this->_oSmarty->assign('lang_file_create_info', LANG_MEDIA_FILE_CREATE_INFO);
    $this->_oSmarty->assign('lang_headline', LANG_MEDIA_FILE_CREATE_TITLE);

		$this->_oSmarty->template_dir = Helper::getTemplateDir('medias' ,'create');
    return $this->_oSmarty->fetch('create.tpl');
  }

  private function _proceedUpload() {
    $oUpload = new Upload($this->_aRequest, $this->_aFile, $this->_aRequest['rename']);
    return $oUpload->uploadFile('media');
  }

  public function show() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

    else {
      $sOriginalPath = PATH_UPLOAD.	'/media';
      $oDir = opendir($sOriginalPath);

      $aFiles = array();
      while($sFile = readdir($oDir)) {
        $sPath = $sOriginalPath.	'/'	.$sFile;

        if(substr($sFile, 0, 1) == '.' || is_dir($sPath))
          continue;

        $sFileType = strtolower(substr(strrchr($sPath, '.'), 1));
        $iNameLen = strlen($sFile) - 4;

        if( $sFileType == 'jpeg')
          $iNameLen--;

        $sFileName = substr($sFile, 0, $iNameLen);

        if(	$sFileType      == 'jpg' || $sFileType  == 'jpeg'|| $sFileType  == 'png' || $sFileType  == 'gif') {
          $aImgDim = getImageSize($sPath);

          if( !file_exists(PATH_UPLOAD.	'/temp/media/'	.$sFile) ) {
            $oImage = new Image($sFileName, 'temp', $sPath, $sFileType);
            $oImage->resizeAndCut('32', 'media');
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

  public function destroy() {
    if( USER_RIGHT < 3 )
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

    else {
      if(is_file(PATH_UPLOAD.	'/media/'	.$this->_aRequest['id'])) {
        unlink(PATH_UPLOAD.	'/media/'	.$this->_aRequest['id']);
        return Helper::successMessage(LANG_MEDIA_FILE_DESTROY_SUCCESSFUL, '/media');
      }
      else
        return Helper::errorMessage(LANG_ERROR_MEDIA_FILE_NOT_AVAIABLE, '/media');
    }
  }
}
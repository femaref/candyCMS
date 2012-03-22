<?php

/**
 * Handle all uploads.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Helper;

use CandyCMS\Helper\Helper as Helper;

class Upload {

  /**
   * file information
   *
   * @var array
   * @access private
   *
   */
  private $_aFile;

  /**
   * @var array
   * @access private
   *
   */
  private $_sFileExtensions = array();

  /**
   * names of the files
   *
   * @var array
   * @access private
   *
   */
  private $_sFileNames = array();

  /**
   * name of the upload folder
   *
   * @var array
   * @access private
   *
   */
  private $_sUploadFolder;

  /**
   * file path for each file
   *
   * @var array
   * @access public
   *
   */
  public $sFilePaths = array();

  /**
   * Fetch the required information.
   *
   * @access public
   * @param array $aRequest alias for the combination of $_GET and $_POST
   * @param array $aFile alias for $_FILE
   * @param string $sRename new file name
   *
   */
  public function __construct(&$aRequest, &$aSession, &$aFile) {
    $this->_aRequest = & $aRequest;
    $this->_aSession = & $aSession;
    $this->_aFile = & $aFile;

    require_once PATH_STANDARD . '/app/helpers/Image.helper.php';
  }

  /**
   * Rename files (if chosen) and upload them afterwards to predefined folder.
   *
   * @access public
   * @param string $sFolder name of upload folder
   * @param boolean $bFilenameHashes whether a hash should be used as the filename
   * @see app/controller/Medias.controller.php
   * @return array(boolean) status of uploads.
   *
   */
  public function uploadFiles($sFolder = 'medias', $bFilenameHashes = false) {
    $sType = isset($this->_aFile['image']) ? 'image' : 'file';

    if (isset($this->_aFile[$sType]) && !empty($this->_aFile[$sType]['name'][0])) {
      $bIsArray = is_array($this->_aFile[$sType]['name']);
      $iFileCount = $bIsArray ? count($this->_aFile[$sType]['name']) : 1;

      $bReturn = array();
      for ($iI = 0; $iI < $iFileCount; $iI++) {

        $sFileName = $bIsArray ? $this->_aFile[$sType]['name'][$iI] : $this->_aFile[$sType]['name'];
        $sFileName = strtolower($sFileName);

        $this->_sFileNames[$iI] = Helper::replaceNonAlphachars($sFileName);
        $this->_sFileExtensions[$iI] = substr(strrchr($sFileName, '.'), 1);

        # remove extension, if there is one
        $iPos = strrpos($this->_sFileNames[$iI], '.');
        if ($iPos) $this->_sFileNames[$iI] = substr($this->_sFileNames[$iI], 0, $iPos);

        # rename the file, if a new name is specified
        if (!empty($this->_aRequest['rename']))
          $this->_sFileNames[$iI] = Helper::replaceNonAlphachars($this->_aRequest['rename']) .
                  ($iFileCount == 1 ? '' : '_' . $iI);

        # generate hash, if wanted
        if ($bFilenameHashes)
          $this->_sFileNames[$iI] = md5($this->_sFileNames[$iI] . rand(000, 999));

        # generate the new filename with its full path
        $this->sFilePaths[$iI] = Helper::removeSlash(PATH_UPLOAD . '/' .  $sFolder . '/' .
                                                    $this->_sFileNames[$iI] . '.' . $this->_sFileExtensions[$iI]);

        # upload the file
        $sTempFileName = $bIsArray ? $this->_aFile[$sType]['tmp_name'][$iI] : $this->_aFile[$sType]['tmp_name'];
        $bReturn[$iI] = (move_uploaded_file($sTempFileName, $this->sFilePaths[$iI])) ? true : false;
      }

      return $bReturn;
    }
    else
      return die(print_r($this->_aFile));
  }

  /**
   * Upload files into an album. Resize and / or cut the files.
   *
   * @access public
   * @param string $sResize cut or resize the images?!
   * @see app/controller/Galleries.controller.php
   * @return array boolean status of each upload
   *
   */
  public function uploadGalleryFiles($sResize = '') {
    $this->_aRequest['cut'] = !empty($sResize) ? $sResize : $this->_aRequest['cut'];
    $this->_sUploadFolder = 'galleries/' . (int) $this->_aRequest['id'];

    $aUploads = $this->uploadFiles($this->_sUploadFolder . '/original', true);

    //do cuts and or resizes
    $iFileCount = count($aUploads);
    for ($iI = 0; $iI < $iFileCount; $iI++) {
      if ($aUploads[$iI] === true) {
        $oImage = new Image($this->_sFileNames[$iI], $this->_sUploadFolder, $this->sFilePaths[$iI], $this->_sFileExtensions[$iI]);

        if (isset($this->_aRequest['cut']) && 'c' == $this->_aRequest['cut'])
          $oImage->resizeAndCut(THUMB_DEFAULT_X);

        elseif (isset($this->_aRequest['cut']) && 'r' == $this->_aRequest['cut'])
          $oImage->resizeDefault(THUMB_DEFAULT_X, THUMB_DEFAULT_Y);

        else
          throw new Exception('No resizing information!');

        $oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y, 'popup');
        $oImage->resizeAndCut('32');
      }
    }

    return $aUploads;
  }

  /**
   * Upload a user avatar.
   *
   * @access public
   * @param boolean $bReturnPath return path information?!
   * @see app/controller/Users.controller.php
   * @return string|boolean user avatar path or boolean status of upload.
   *
   */
  public function uploadAvatarFile($bReturnPath = true) {
    $this->_aRequest['rename'] = isset($this->_aRequest['id']) && $this->_aSession['user']['role'] == 4 ?
            (int) $this->_aRequest['id'] :
            $this->_aSession['user']['id'];

    if ($this->_aFile['image']['size'] > 409600)
      return Helper::errorMessage(LANG_ERROR_MEDIA_MAX_FILESIZE_REACHED);

    else {
      $this->destroyAvatarFiles($this->_aRequest['rename']);

      $this->_sUploadFolder = 'users';
      $aUploads = $this->uploadFiles($this->_sUploadFolder . '/original');

      # upload might have failed
      if ($aUploads[0] === false)
        return false;

      # upload was successfull
      $oImage = & new Image($this->_sFileNames[0], $this->_sUploadFolder, $this->sFilePaths[0], $this->_sFileExtensions[0]);

      $oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y, 'popup');
      $oImage->resizeDefault(THUMB_DEFAULT_X, THUMB_DEFAULT_Y);
      $oImage->resizeDefault(100);
      $oImage->resizeAndCut(64);
      $oImage->resizeAndCut(32);

      return $bReturnPath ? $this->_sFilePaths[0] : $aUploads[0];
    }
  }

  /**
   * Delete user avatars.
   *
   * @static
   * @access public
   * @param string $sFileName name of the file
   *
   */
  public static function destroyAvatarFiles($sFileName) {
    $aFileTypes = array('jpg', 'png', 'gif');
    $aFolders = array('original', 'popup', THUMB_DEFAULT_X, '100', '64', '32');

    foreach ($aFileTypes as &$sExtension) {
      foreach ($aFolders as &$sFolder) {
        if (is_file(Helper::removeSlash(PATH_UPLOAD . '/users/' . $sFolder . '/' . $sFileName . '.' . $sExtension)))
          unlink(Helper::removeSlash(PATH_UPLOAD . '/users/' . $sFolder . '/' . $sFileName . '.' . $sExtension));
      }
    }
  }

  /**
   * Return the files extensions.
   *
   * @return string $this->_sFileExtension file extension.
   *
   */
  public function getExtensions() {
    return $this->_sFileExtensions;
  }

  /**
   * Return the current file.
   *
   * @param boolean $bWithExtension
   * @return array with all filenames
   *
   */
  public function getIds($bWithExtension = true) {
    if ($bWithExtension) {
      for ($iI = 0; $iI < count($this->_sFileNames); $iI++)
        $aReturn[$iI] = $this->_sFileNames[$iI] . '.' . $this->_sFileExtensions[$iI];

      return isset($aReturn) ? $aReturn : array();
    }
    else
      return $this->_sFileNames;
  }
}
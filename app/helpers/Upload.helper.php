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
	 * @var string
	 * @access private
	 *
	 */
	private $_sFileExtension = '';

	/**
	 * name of the file
	 *
	 * @var string
	 * @access private
	 *
	 */
	private $_sFileName;

	/**
	 * name of the upload folder
	 *
	 * @var string
	 * @access private
	 *
	 */
	private $_sUploadFolder;

	/**
	 * file path
	 *
	 * @var string
	 * @access public
	 *
	 */
	public $sFilePath;

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
    $this->_aRequest  = & $aRequest;
    $this->_aSession	= & $aSession;
    $this->_aFile     = & $aFile;

    require_once PATH_STANDARD . '/app/helpers/Image.helper.php';
  }

	/**
	 * Rename file (if chosen) and upload it afterwards to predefined folder.
	 *
	 * @access public
	 * @param string $sFolder name of upload folder
	 * @see app/controller/Medias.controller.php
	 * @return boolean status of upload.
	 *
	 */
  public function uploadFile($sFolder = 'medias') {
    if (isset($this->_aFile['file']) && !empty($this->_aFile['file']['name'][0])) {
      for ($iI = 0; $iI < count($this->_aFile['file']['name']); $iI++) {
        $this->_sFileName       = Helper::replaceNonAlphachars(strtolower($this->_aFile['file']['name'][$iI]));
        $this->_sFileExtension  = strtolower(substr(strrchr($this->_aFile['file']['name'][$iI], '.'), 1));

        if (!empty($this->_aRequest['rename']) && $iI == 0)
          $this->_sFileName = Helper::replaceNonAlphachars($this->_aRequest['rename']) . '.' . $this->_sFileExtension;

        elseif (!empty($this->_aRequest['rename']) && $iI > 0)
          $this->_sFileName = Helper::replaceNonAlphachars($this->_aRequest['rename']) . '_' . $iI . '.' . $this->_sFileExtension;


        $this->sFilePath = Helper::removeSlash(PATH_UPLOAD . '/' . $sFolder . '/' . $this->_sFileName);
        return move_uploaded_file($this->_aFile['file']['tmp_name'][$iI], $this->sFilePath);
      }
    }
  }

	/**
	 * Upload a file into an album. Resize and / or cut the file.
	 *
	 * @access public
	 * @param string $sResize cut or resize the image?!
	 * @see app/controller/Galleries.controller.php
	 * @return boolean status of upload
	 *
	 */
  public function uploadGalleryFile($sResize = '') {
    $this->_aRequest['cut'] = !empty($sResize) ? $sResize : $this->_aRequest['cut'];
		$this->_sFileExtension	= strtolower(substr(strrchr($this->_aFile['name'], '.'), 1));
		$this->_sFileName				= md5($this->_sFileName . rand(000, 999));
		$this->_sUploadFolder		= 'gallery/' . (int) $this->_aRequest['id'];
		$this->sFilePath				= Helper::removeSlash(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' .
						$this->_sFileName . '.' . $this->_sFileExtension);

		$bReturn = move_uploaded_file($this->_aFile['tmp_name'], $this->sFilePath);

		$oImage = new Image($this->_sFileName, $this->_sUploadFolder, $this->sFilePath, $this->_sFileExtension);
		if (isset($this->_aRequest['cut']) && 'c' == $this->_aRequest['cut'])
			$oImage->resizeAndCut(THUMB_DEFAULT_X);

		elseif (isset($this->_aRequest['cut']) && 'r' == $this->_aRequest['cut'])
			$oImage->resizeDefault(THUMB_DEFAULT_X, THUMB_DEFAULT_Y);

		else
			throw new Exception('No resizing information!');

		$oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y, 'popup');
		$oImage->resizeAndCut('32');

		return $bReturn;
  }

	/**
	 * Upload a user avatar.
	 *
	 * @access public
	 * @param boolean $bReturnPath return path information?!
	 * @see app/controller/Users.controller.php
	 * @return string|boolean user avatar path or boolean status if upload.
	 *
	 */
  public function uploadAvatarFile($bReturnPath = true) {
    $this->_sFileExtension = strtolower(substr(strrchr($this->_aFile['image']['name'], '.'), 1));
    $this->_sFileName = isset($this->_aRequest['id']) && $this->_aSession['user']['role'] == 4 ?
            (int) $this->_aRequest['id'] :
            $this->_aSession['user']['id'];

    $this->_sUploadFolder = 'user';
    $this->_sFilePath = Helper::removeSlash(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_sFileName);

    if ($this->_aFile['image']['size'] > 409600)
      return Helper::errorMessage(LANG_ERROR_MEDIA_MAX_FILESIZE_REACHED);

    else {
      $this->_deleteAvatarFiles();
      $bReturn = move_uploaded_file($this->_aFile['image']['tmp_name'], $this->_sFilePath . '.' . $this->_sFileExtension);

      $oImage = new Image($this->_sFileName, $this->_sUploadFolder, $this->_sFilePath . '.' . $this->_sFileExtension, $this->_sFileExtension);
      $oImage->resizeDefault(POPUP_DEFAULT_X, POPUP_DEFAULT_Y, 'popup');
      $oImage->resizeDefault(THUMB_DEFAULT_X);
      $oImage->resizeDefault('100');
      $oImage->resizeAndCut('64');
      $oImage->resizeAndCut('32');

			return $bReturnPath === true ? $this->_sFilePath . '.' . $this->_sFileExtension : $bReturn;
    }
  }

	/**
	 * Delete user avatars.
   *
	 * @access private
	 *
	 */
  private function _deleteAvatarFiles() {
    $aFileTypes = array('jpg', 'png', 'gif');

    $aFiles = array('original/' . $this->_sFileName,
        'popup/' . $this->_sFileName,
        THUMB_DEFAULT_X . '/' . $this->_sFileName,
        '100/' . $this->_sFileName,
        '64/' . $this->_sFileName,
        '32/' . $this->_sFileName
    );

    foreach ($aFileTypes as &$sExtension) {
      foreach ($aFiles as &$sFile) {
        if (is_file(Helper::removeSlash(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . $sFile . '.' . $sExtension)))
          unlink(Helper::removeSlash(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . $sFile . '.' . $sExtension));
      }
    }
  }

	/**
	 * Return the files extension.
	 *
	 * @return string $this->_sFileExtension file extension.
	 *
	 */
  public function getExtension() {
    return $this->_sFileExtension;
  }

	/**
	 * Return the current file.
	 *
	 * @param boolean $bWithExtension
	 * @return type
	 *
	 */
  public function getId($bWithExtension = true) {
		return	$bWithExtension === true ?
						$this->_sFileName . '.' . $this->_sFileExtension :
						$this->_sFileName;
	}
}
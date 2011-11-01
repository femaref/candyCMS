<?php

/**
 * Handle all uploads.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Helper;

use CandyCMS\Helper\Helper as Helper;

class Upload {

  /**
	 * file information
	 *
	 * @var array
	 * @access private
	 */
	private $_aFile;

	/**
	 * @var string
	 * @access private
	 */
	private $_sFileExtension = '';

	/**
	 * name of the file
	 *
	 * @var string
	 * @access private
	 */
	private $_sFileName;

	/**
	 * new name of the file
	 *
	 * @var string
	 * @access private
	 */
	private $_sRename;

	/**
	 * name of the upload folder
	 *
	 * @var string
	 * @access private
	 */
	private $_sUploadFolder;

	/**
	 * file path
	 *
	 * @var string
	 * @access public
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
  public function __construct($aRequest, $aFile, $sRename = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aFile     = & $aFile;
    $this->_sRename   = & $sRename;
  }

	/**
	 * Rename file (if chosen) and upload it afterwards to predefined folder.
	 *
	 * @access public
	 * @param string $sFolder name of upload folder
	 * @see app/controller/Media.controller.php
	 * @return boolean status of upload.
	 *
	 */
  public function uploadFile($sFolder = 'media') {
    if (isset($this->_aFile['file']) && !empty($this->_aFile['file']['name'][0])) {


      for ($iI = 0; $iI < count($this->_aFile['file']['name']); $iI++) {
        $this->_sFileName       = & Helper::replaceNonAlphachars(strtolower($this->_aFile['file']['name'][$iI]));
        $this->_sFileExtension  = & strtolower(substr(strrchr($this->_aFile['file']['name'][$iI], '.'), 1));

        if (!empty($this->_sRename) && $iI == 0)
          $this->_sFileName = Helper::replaceNonAlphachars($this->_sRename) . '.' . $this->_sFileExtension;

        elseif (!empty($this->_sRename) && $iI > 0)
          $this->_sFileName = Helper::replaceNonAlphachars($this->_sRename) . '_' . $iI . '.' . $this->_sFileExtension;

        $this->sFilePath = PATH_UPLOAD . '/' . $sFolder . '/' . $this->_sFileName;
        $bReturn = & move_uploaded_file($this->_aFile['file']['tmp_name'][$iI], $this->sFilePath);
      }

      return $bReturn;
    }
  }

	/**
	 * Upload a file into an album. Resize and / or cut the file.
	 *
	 * @access public
	 * @param string $sResize cut or resize the image?!
	 * @see app/controller/Gallery.controller.php
	 * @return boolean status of upload.
	 *
	 */
  public function uploadGalleryFile($sResize = '') {
    $this->_aRequest['cut'] = !empty($sResize) ? $sResize : $this->_aRequest['cut'];
		$this->_sFileExtension	= strtolower(substr(strrchr($this->_aFile['name'], '.'), 1));
		$this->_sFileName				= md5($this->_sFileName . rand(000, 999));
		$this->_sUploadFolder		= 'gallery/' . (int) $this->_aRequest['id'];
		$this->sFilePath				= PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' .
						$this->_sFileName . '.' . $this->_sFileExtension;

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
	 * @see app/controller/User.controller.php
	 * @return string|boolean user avatar path or boolean status if upload.
	 *
	 */
  public function uploadAvatarFile($bReturnPath = true) {
    $this->_sFileExtension = strtolower(substr(strrchr($this->_aFile['image']['name'], '.'), 1));
    $this->_sFileName = isset($this->_aRequest['id']) && USER_RIGHT == 4 ? (int)$this->_aRequest['id'] : USER_ID;
    $this->_sUploadFolder = 'user';
    $this->_sFilePath = PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_sFileName;

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

      if ($bReturnPath == true)
        return $this->_sFilePath . '.' . $this->_sFileExtension;
      else
        return $bReturn;
    }
  }

  private function _deleteAvatarFiles() {
    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_sFileName . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_sFileName . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_sFileName . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_sFileName . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_sFileName . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_sFileName . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_sFileName . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_sFileName . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_sFileName . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_sFileName . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_sFileName . '.jpg'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_sFileName . '.jpg');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_sFileName . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_sFileName . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_sFileName . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_sFileName . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_sFileName . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_sFileName . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_sFileName . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_sFileName . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_sFileName . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_sFileName . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_sFileName . '.png'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_sFileName . '.png');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_sFileName . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/original/' . $this->_sFileName . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_sFileName . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/popup/' . $this->_sFileName . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_sFileName . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/' . THUMB_DEFAULT_X . '/' . $this->_sFileName . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_sFileName . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/100/' . $this->_sFileName . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_sFileName . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/64/' . $this->_sFileName . '.gif');

    if (is_file(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_sFileName . '.gif'))
      unlink(PATH_UPLOAD . '/' . $this->_sUploadFolder . '/32/' . $this->_sFileName . '.gif');
  }

  public function getExtension() {
    return $this->_sFileExtension;
  }

  # Return the current file
  public function getId($bWithExtension = true) {
    if ($bWithExtension == true)
      return $this->_sFileName . '.' . $this->_sFileExtension;
    else
      return $this->_sFileName;
  }
}
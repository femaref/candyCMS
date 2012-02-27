<?php

/**
 * CRUD actions for gallery overview and gallery albums.
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
use CandyCMS\Helper\Upload as Upload;
use CandyCMS\Model\Gallery as Model;
use Smarty;

class Gallery extends Main {

  /**
   * Include the gallery model.
   *
   * @access public
   *
   */
  public function __init() {
    # Override template folder.
    $this->_sTemplateFolder = 'galleries';

    require PATH_STANDARD . '/app/models/Gallery.model.php';
    $this->_oModel = new Model($this->_aRequest, $this->_aSession, $this->_aFile);
  }

  /**
   * Show gallery album or album overview (depends on a given ID or not).
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    # Album images
    if (!empty($this->_iId) && !isset($this->_aRequest['album_id'])) {
      $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'files');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'files');

      $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
			$this->oSmarty->setCacheLifetime(300);
			$this->oSmarty->setTemplateDir($sTemplateDir);

      # Collect data array
      $sAlbumName					= Model::getAlbumName($this->_iId);
      $sAlbumDescription	= Model::getAlbumContent($this->_iId);

			if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
				# Get data and count afterwards
				$aData = & $this->_oModel->getThumbs($this->_iId);

				$this->oSmarty->assign('files', $aData);
				$this->oSmarty->assign('file_no', count($aData));
				$this->oSmarty->assign('gallery_name', $sAlbumName);
				$this->oSmarty->assign('gallery_content', $sAlbumDescription);

				$this->_setDescription($sAlbumDescription);
				$this->_setTitle($this->_removeHighlight($sAlbumName) . ' - ' . I18n::get('global.gallery'));
			}

      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }

    # Specific image
    elseif (!empty($this->_iId) && isset($this->_aRequest['album_id'])) {
      $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'image');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'image');

      $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
			$this->oSmarty->setCacheLifetime(300);
			$this->oSmarty->setTemplateDir($sTemplateDir);

			if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
				$aData = & Model::getFileData($this->_iId);

				# Absolute URL for image information
				$sUrl = Helper::removeSlash(PATH_UPLOAD . '/gallery/' . $this->_aRequest['album_id'] .
								'/popup/' . $aData['file']);

				if (file_exists($sUrl)) {
					# Get image information
					$aImageInfo = getimagesize($sUrl);

					$aData['url']    = Helper::addSlash($sUrl);
					$aData['width']  = $aImageInfo[0];
					$aData['height'] = $aImageInfo[1];

					$this->_setDescription($aData['content']);
					$this->_setTitle(I18n::get('global.image.image') . ': ' . $aData['file']);

					$this->oSmarty->assign('i', $aData);

					return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
				}
				else
					Helper::redirectTo('/error/404');
			}
    }

    # Album overview
    else {
      $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'albums');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'albums');

      $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
			$this->oSmarty->setCacheLifetime(300);
			$this->oSmarty->setTemplateDir($sTemplateDir);

			if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
				$this->_setDescription(I18n::get('global.gallery'));
				$this->_setTitle(I18n::get('global.gallery'));

				$this->oSmarty->assign('albums', $this->_oModel->getData());
				$this->oSmarty->assign('_pages_', $this->_oModel->oPagination->showPages());
			}

      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
  }

  /**
   * Build form template to create or update a gallery album.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormTemplate() {
    $aData = & $this->_oModel->getData($this->_iId, true);

    if (!empty($this->_iId))
      $this->_setTitle($aData['title']);

    else {
      $aData['title']    = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
      $aData['content']  = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
    }

    foreach ($aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, '_form_album');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form_album');

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Create a gallery album.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, activate the model, insert them into the database and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create() {
    $this->_setError('title');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->create() === true) {
      $iId    = $this->_oModel->getLastInsertId('gallery_albums');
      $sPath  = Helper::removeSlash(PATH_UPLOAD . '/gallery/' . $iId);

      $sPathThumbS = $sPath . '/32';
      $sPathThumbL = $sPath . '/' . THUMB_DEFAULT_X;
      $sPathThumbP = $sPath . '/popup';
      $sPathThumbO = $sPath . '/original';

      if (!is_dir($sPath))
        mkdir($sPath, 0755);

      if (!is_dir($sPathThumbS))
        mkdir($sPathThumbS, 0755);

      if (!is_dir($sPathThumbL))
        mkdir($sPathThumbL, 0755);

      if (!is_dir($sPathThumbP))
        mkdir($sPathThumbP, 0755);

      if (!is_dir($sPathThumbO))
        mkdir($sPathThumbO, 0755);

      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $iId, $this->_aSession['userdata']['id']);
      return Helper::successMessage(I18n::get('success.create'), '/gallery/' . $iId);
    }

    else
      return Helper::errorMessage(I18n::get('error.sql'), '/gallery');
  }

  /**
   * Update a gallery album.
   *
   * Activate model, insert data into the database and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _update() {
    $this->_setError('title');

    $sRedirect = '/gallery/' . (int) $this->_aRequest['id'];

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'],
									$this->_aRequest['action'],
									(int) $this->_aRequest['id'],
									$this->_aSession['userdata']['id']);

      return Helper::successMessage(I18n::get('success.update'), $sRedirect);
    }

    else
      return Helper::errorMessage(I18n::get('error.sql'), $sRedirect);
  }

  /**
   * Destroy a gallery album.
   *
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    if($this->_oModel->destroy($this->_iId) === true) {
      Log::insert($this->_aRequest['section'],
									$this->_aRequest['action'],
									$this->_iId,
									$this->_aSession['userdata']['id']);

      return Helper::successMessage(I18n::get('success.destroy'), '/gallery');
    }

    else {
			# Fix redirect to gallery main page.
      unset($this->_iId);
      return Helper::errorMessage(I18n::get('error.sql'), '/gallery');
    }
  }

  /**
   * Build form template to upload or update a file.
   * NOTE: We need to get the request action because we already have an gallery album ID.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormFileTemplate() {
    # Update
    if ($this->_aRequest['action'] == 'updatefile') {
      $aDetails = Model::getFileDetails($this->_iId);
      $this->oSmarty->assign('content', Helper::formatOutput($aDetails['content']));
      $this->oSmarty->assign('album_id', Helper::formatOutput($aDetails['album_id']));
    }
    # Create
    else {
      # See helper/Image.helper.php for details!
      # r = resize, c = cut
      $this->oSmarty->assign('default', isset($this->_aRequest['cut']) ?
											Helper::formatInput($this->_aRequest['cut']) :
											'c');
      $this->oSmarty->assign('content', isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '');
    }

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, '_form_file');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form_file');

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Create a gallery entry.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, activate the model, insert them into the database and redirect afterwards.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function createFile() {
    if ($this->_aSession['userdata']['role'] < 3)
      return Helper::errorMessage(I18n::get('error.missing.permission'));

    else {
      if (isset($this->_aRequest['createfile_gallery'])) {
        if ($this->_createFile() === true) {
          # Log uploaded image. Request ID = album id
          Log::insert($this->_aRequest['section'],
											'createfile',
											(int) $this->_aRequest['id'],
											$this->_aSession['userdata']['id']);

          return Helper::successMessage(I18n::get('success.file.upload'), '/gallery/' . $this->_iId);
        }
        else
          return Helper::errorMessage(I18n::get('error.file.upload'), '/gallery/' . $this->_iId . '/createfile');
      }
      else
        return $this->_showFormFileTemplate();
    }
  }

  /**
   * Upload each selected file.
   *
   * @access private
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  private function _createFile() {
    require PATH_STANDARD . '/app/helpers/Upload.helper.php';

    if (isset($this->_aFile['file']) && !empty($this->_aFile['file']['name'][0])) {
      for ($iI = 0; $iI < count($this->_aFile['file']['name']); $iI++) {
        $aFile['name']      = $this->_aFile['file']['name'][$iI];
        $aFile['type']      = $this->_aFile['file']['type'][$iI];
        $aFile['tmp_name']  = $this->_aFile['file']['tmp_name'][$iI];
        $aFile['error']     = $this->_aFile['file']['error'][$iI];
        $aFile['size']      = $this->_aFile['file']['size'][$iI];

        $oUploadFile = new Upload($this->_aRequest, $this->_aSession, $aFile);

        if ($oUploadFile->uploadGalleryFile() === true)
          $this->_oModel->createFile($oUploadFile->getId(), $oUploadFile->getExtension());
      }

      return true;
    }
    else {
      $this->_aError['file'] = I18n::get('error.form.missing.file');
      return $this->_showFormFileTemplate();
    }
  }

  /**
   * Update a gallery entry.
   *
   * Activate model, insert data into the database and redirect afterwards.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function updateFile() {
    if ($this->_aSession['userdata']['role'] < 3)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/gallery');

    else {
      if (isset($this->_aRequest['updatefile_gallery'])) {
        if ($this->_oModel->updateFile($this->_iId) === true) {
          Log::insert($this->_aRequest['section'],
											$this->_aRequest['action'],
											(int) $this->_iId,
											$this->_aSession['userdata']['id']);

          return Helper::successMessage(I18n::get('success.update'), '/gallery');
        }
        else
          return Helper::errorMessage(I18n::get('error.sql'), '/gallery');
      }
      else
        return $this->_showFormFileTemplate();
    }
  }

  /**
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function destroyFile() {
    if ($this->_aSession['userdata']['role'] < 3)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/gallery/' . (int) $this->_aRequest['album_id']);

    else {
      if($this->_oModel->destroyFile($this->_iId) === true) {
        Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId, $this->_aSession['userdata']['id']);
        unset($this->_iId);
        return Helper::successMessage(I18n::get('success.destroy'), '/gallery/' . (int) $this->_aRequest['album_id']);
      }
      else
        return Helper::errorMessage(I18n::get('error.sql'), '/gallery/' . (int) $this->_aRequest['album_id']);
    }
  }
}
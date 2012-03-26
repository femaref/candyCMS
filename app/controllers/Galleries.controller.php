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
use CandyCMS\Helper\SmartySingleton as SmartySingleton;

class Galleries extends Main {

  /**
   * Route to right action.
   *
   * @access public
   * @return string HTML
   *
   */
  public function show() {
    if (!isset($this->_aRequest['action']))
      $this->_aRequest['action'] = 'image';

    switch ($this->_aRequest['action']) {

      case 'createfile':

        $this->setTitle(I18n::get('gallery.files.title.create'));
        return $this->createFile();

        break;

      case 'updatefile':

        $this->setTitle(I18n::get('gallery.files.title.update'));
        return $this->updateFile();

        break;

      case 'destroyfile':

        $this->setTitle(I18n::get('gallery.files.title.destroy'));
        return $this->destroyFile();

        break;

      default:
      case 'image':

        $this->oSmarty->setCaching(SmartySingleton::CACHING_LIFETIME_SAVED);
        return $this->_show();

        break;
    }
  }

  /**
   * Show image, gallery album or overview (depends on a given ID and album_id).
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    # Album images
    if ($this->_iId && !isset($this->_aRequest['album_id']))
      return $this->_showAlbum();

    # Specific image
    elseif ($this->_iId && isset($this->_aRequest['album_id']))
      return $this->_showImage();

    # Album overview
    else
      return $this->_showOverview();
  }

  /**
   * Show overview of albums.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showOverview() {
    $sTemplateDir   = Helper::getTemplateDir($this->_aRequest['controller'], 'albums');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'albums');

    if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
      $this->oSmarty->assign('albums', $this->_oModel->getData());
      $this->oSmarty->assign('_pages_', $this->_oModel->oPagination->showPages('/' . $this->_aRequest['controller']));
    }

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Show overview of images in one album.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showAlbum() {
    $sTemplateDir   = Helper::getTemplateDir($this->_aRequest['controller'], 'files');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'files');

    # Collect data array
    $sAlbumName = $this->_oModel->getAlbumName($this->_iId, $this->_aRequest);
    $sAlbumDescription = $this->_oModel->getAlbumContent($this->_iId, $this->_aRequest);

    $this->setTitle($this->_removeHighlight($sAlbumName) . ' - ' . I18n::get('global.gallery'));
    $this->setDescription($this->_removeHighlight($sAlbumDescription));

    if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
      $aData = $this->_oModel->getThumbs($this->_iId);

      $this->oSmarty->assign('files', $aData);
      $this->oSmarty->assign('file_no', count($aData));
      $this->oSmarty->assign('gallery_name', $sAlbumName);
      $this->oSmarty->assign('gallery_content', $sAlbumDescription);
    }

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Show one specific Image.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showImage() {
    $sTemplateDir   = Helper::getTemplateDir($this->_aRequest['controller'], 'image');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'image');

    $aData = $this->_oModel->getFileData($this->_iId);

    # Absolute URL for image information
    $sUrl = Helper::removeSlash(PATH_UPLOAD . '/' . $this->_aRequest['controller'] . '/' . $this->_aRequest['album_id'] .
                    '/popup/' . $aData['file']);

    if (file_exists($sUrl) || WEBSITE_MODE == 'test') {
      # Get image information
      $aImageInfo = getimagesize($sUrl);

      $aData['url'] = Helper::addSlash($sUrl);
      $aData['width'] = $aImageInfo[0];
      $aData['height'] = $aImageInfo[1];

      $this->oSmarty->assign('i', $aData);

      $this->setTitle(I18n::get('global.image.image') . ': ' . $aData['file']);
      $this->setDescription($aData['content']);

      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }

    else
      Helper::redirectTo('/errors/404');
  }

  /**
   * Build form template to create or update a gallery album.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormTemplate() {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], '_form_album');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form_album');

    if ($this->_iId) {
			$aData = $this->_oModel->getData($this->_iId, true);

      $this->setTitle(I18n::get('galleries.albums.title.update', $aData['title']));
    }
		else {
      $aData['title']    = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
      $aData['content']  = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';

      $this->setTitle(I18n::get('galleries.albums.title.create'));
    }

    foreach ($aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Create a gallery album.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create() {
    $this->_setError('title');

    if ($this->_aError)
      return $this->_showFormTemplate();

    elseif ($this->_oModel->create() === true) {
      $this->oSmarty->clearCacheForController($this->_aRequest['controller']);
      $this->oSmarty->clearCacheForController('searches');

      $iId    = $this->_oModel->getLastInsertId('gallery_albums');
      $sPath  = Helper::removeSlash(PATH_UPLOAD . '/' . $this->_aRequest['controller'] . '/' . $iId);

      # Create missing thumb folders.
      $aThumbs = array('32', THUMB_DEFAULT_X, 'popup', 'original');
      foreach($aThumbs as $sFolder) {
        if (!is_dir($sPath . '/' . $sFolder))
          mkdir($sPath . '/' . $sFolder, 0755, true);
      }

      Logs::insert( $this->_aRequest['controller'],
                    $this->_aRequest['action'],
                    $iId,
                    $this->_aSession['user']['id']);

      return Helper::successMessage(I18n::get('success.create'), '/' . $this->_aRequest['controller'] . '/' . $iId);
    }

    else
      return Helper::errorMessage(I18n::get('error.sql'), '/' . $this->_aRequest['controller']);
  }

  /**
   * Build form template to upload or update a file.
   * NOTE: We need to get the request action because we already have an gallery album ID.
   *
   * @access protected
   * @return string HTML content
   * @see app/helper/Image.helper.php
   *
   */
  protected function _showFormFileTemplate() {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], '_form_file');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form_file');

    # Update
    if ($this->_aRequest['action'] == 'updatefile') {
      $aDetails = $this->_oModel->getFileDetails($this->_iId);

      $this->oSmarty->assign('content', Helper::formatOutput($aDetails['content']));
      $this->oSmarty->assign('album_id', Helper::formatOutput($aDetails['album_id']));
    }

    # Create
    else {
      # r = resize, c = cut
      $this->oSmarty->assign('default', isset($this->_aRequest['cut']) ?
											Helper::formatInput($this->_aRequest['cut']) :
											'c');

      $this->oSmarty->assign('content', isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '');
    }

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Create a gallery entry.
   *
   * Create entry or show form template if we have enough rights.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function createFile() {
    if ($this->_aSession['user']['role'] < 3)
      return Helper::errorMessage(I18n::get('error.missing.permission'));

    return isset($this->_aRequest['createfile_galleries']) ?
            $this->_createFile() :
            $this->_showFormFileTemplate();
  }

  /**
   * Create a gallery entry.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, upload each selected file, insert them into the database and redirect afterwards.
   *
   * @access private
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  private function _createFile() {
    require PATH_STANDARD . '/app/helpers/Upload.helper.php';

    $this->_setError('file');

    if ($this->_aError)
      return $this->_showFormFileTemplate();

    else {
      $oUploadFile = & new Upload($this->_aRequest, $this->_aSession, $this->_aFile);

      $aReturnValues = $oUploadFile->uploadGalleryFiles();

      $aIds = $oUploadFile->getIds(false);
      $aExts = $oUploadFile->getExtensions();

      $iFileCount = count($aReturnValues);
      $bReturnVal = true;
      for ($iI = 0; $iI < $iFileCount; $iI++)
        if ($aReturnValues[$iI] === true)
          $bReturnVal = $bReturnVal && $this->_oModel->createFile($aIds[$iI] . '.' . $aExts[$iI], $aExts[$iI]);
        else
          $bReturnVal = false;

      if ($bReturnVal) {
        $this->oSmarty->clearCacheForController($this->_aRequest['controller']);

        # Log uploaded image. Request ID = album id
        Logs::insert($this->_aRequest['controller'],
                'createfile',
                (int) $this->_aRequest['id'],
                $this->_aSession['user']['id']);


        return Helper::successMessage(I18n::get('success.file.upload'),
                        '/' . $this->_aRequest['controller'] .
                        '/' . $this->_iId);
      }
      else
          return Helper::errorMessage(I18n::get('error.file.upload'),
                        '/' . $this->_aRequest['controller'] .
                        '/' . $this->_iId . '/createfile');
    }
  }

  /**
   * Update a gallery entry.
   *
   * calls _updateFile if data is given, otherwise calls _showFormFileTemplate()
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function updateFile() {
    if ($this->_aSession['user']['role'] < 3)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/' . $this->_aRequest['controller']);

    else {
      return isset($this->_aRequest['updatefile_galleries']) ?
              $this->_updateFile() :
              $this->_showFormFileTemplate();
    }
  }

  /**
   * Update a gallery entry.
   *
   * Activate model, Update data in the database and redirect afterwards.
   *
   * @access private
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  private function _updateFile() {
  //  $this->_setError('content');

    if ($this->_aError)
      return $this->_showFormFileTemplate();

    $aDetails = $this->_oModel->getFileDetails($this->_iId);
    $sRedirectPath = '/' . $this->_aRequest['controller'] . '/' . $aDetails['album_id'];

    if ($this->_oModel->updateFile($this->_iId) === true) {
			$this->oSmarty->clearCacheForController($this->_aRequest['controller']);

      Logs::insert(	$this->_aRequest['controller'],
										$this->_aRequest['action'],
										(int) $this->_aRequest['id'],
										$this->_aSession['user']['id']);

      return Helper::successMessage(I18n::get('success.update'), $sRedirectPath);
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), $sRedirectPath);
  }

  /**
	 * Destroy a gallery entry.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function destroyFile() {
    if ($this->_aSession['user']['role'] < 3)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/' .
              $this->_aRequest['controller'] . '/' . (int) $this->_aRequest['album_id']);

    else {
      if($this->_destroyFile() === true) {
				$this->oSmarty->clearCacheForController($this->_aRequest['controller']);

        Logs::insert($this->_aRequest['controller'],
										$this->_aRequest['action'],
										(int) $this->_iId,
										$this->_aSession['user']['id']);

				unset($this->_iId);
        return Helper::successMessage(I18n::get('success.destroy'), '/' . $this->_aRequest['controller'] . '/' .
                (int) $this->_aRequest['album_id']);
      }
      else
        return Helper::errorMessage(I18n::get('error.sql'), '/' . $this->_aRequest['controller'] . '/' .
                (int) $this->_aRequest['album_id']);
    }
  }

  /**
	 * Destroy a gallery entry.
   *
   * @access private
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  private function _destroyFile() {
    return $this->_oModel->destroyFile($this->_iId) === true;
  }

	/**
	 * Update an album .
	 *
	 * @access protected
	 * @return boolean status of model action
	 *
	 */
	protected function _update() {
		$this->_setError('content');

    return parent::_update('searches');
	}

  /**
	 * Destroy an album.
	 *
	 * @access protected
	 * @return boolean status of model action
	 *
	 */
	protected function _destroy() {
    return parent::_destroy('searches');
	}

}
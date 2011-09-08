<?php

/**
 * CRUD actions for gallery overview and gallery albums.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

require_once 'app/models/Gallery.model.php';
require_once 'app/helpers/Page.helper.php';
require_once 'app/helpers/Upload.helper.php';
require_once 'app/helpers/Image.helper.php';

class Gallery extends Main {

  /**
   * Include the gallery model.
   *
   * @access public
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init() {
    $this->_oModel = new \CandyCMS\Model\Gallery($this->_aRequest, $this->_aSession, $this->_aFile);
  }

  /**
   * Show gallery album or album overview (depends on a given ID or not).
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    # Language
    $this->_oSmarty->assign('lang_create_file_headline', LANG_GALLERY_FILE_CREATE_TITLE);
    $this->_oSmarty->assign('lang_no_files_uploaded', LANG_ERROR_GALLERY_NO_FILES_UPLOADED);

    # Album images
    if (!empty($this->_iId)) {
      # collect data array
      $sAlbumName = \CandyCMS\Model\Gallery::getAlbumName($this->_iId);
      $sAlbumDescription = \CandyCMS\Model\Gallery::getAlbumContent($this->_iId);

      # Get data and count afterwards
      $this->_aData = $this->_oModel->getThumbs($this->_iId);

      $this->_oSmarty->assign('files', $this->_aData);
      $this->_oSmarty->assign('file_no', count($this->_aData));
      $this->_oSmarty->assign('gallery_name', $sAlbumName);
      $this->_oSmarty->assign('gallery_content', $sAlbumDescription);

      $this->_setDescription($sAlbumDescription);
      $this->_setTitle($this->_removeHighlight(LANG_GLOBAL_GALLERY . ': ' . $sAlbumName));

      $this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('galleries' ,'files');
      return $this->_oSmarty->fetch('files.tpl');
    }
    # Album overview
    else {
      $this->_setDescription(LANG_GLOBAL_GALLERY);
      $this->_setTitle(LANG_GLOBAL_GALLERY);

      $this->_oSmarty->assign('albums', $this->_oModel->getData());
      $this->_oSmarty->assign('_pages_', $this->_oModel->oPage->showPages('/gallery'));

      # Language
      $this->_oSmarty->assign('lang_create_album_headline', LANG_GALLERY_ALBUM_CREATE_TITLE);
      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_GALLERY);

      $this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('galleries' ,'albums');
      return $this->_oSmarty->fetch('albums.tpl');
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
    $this->_aData = $this->_oModel->getData($this->_iId, true);

    if (!empty($this->_iId)) {
      # Language
      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
      $this->_oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);

      $this->_setTitle(\CandyCMS\Helper\Helper::removeSlahes($this->_aData['title']));
    }
    else {
      $this->_aData['title']        = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
      $this->_aData['description']  = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';

      # Language
      $this->_oSmarty->assign('lang_headline', LANG_GALLERY_ALBUM_CREATE_TITLE);
      $this->_oSmarty->assign('lang_submit', LANG_GALLERY_ALBUM_CREATE_TITLE);
    }

    foreach ($this->_aData as $sColumn => $sData)
      $this->_oSmarty->assign($sColumn, $sData);

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    $this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('galleries', '_form_album');
    return $this->_oSmarty->fetch('_form_album.tpl');
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
      \CandyCMS\Controller\Log::insert($this->_aRequest['section'], $this->_aRequest['action'], \CandyCMS\Helper\Helper::getLastEntry('gallery_albums'));
      return \CandyCMS\Helper\Helper::successMessage(LANG_SUCCESS_CREATE, '/gallery');
    }

    else
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/gallery');
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
      \CandyCMS\Controller\Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id']);
      return \CandyCMS\Helper\Helper::successMessage(LANG_SUCCESS_UPDATE, $sRedirect);
    }

    else
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
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
      \CandyCMS\Controller\Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $this->_iId);
      return \CandyCMS\Helper\Helper::successMessage(LANG_SUCCESS_DESTROY, '/gallery');
    }

    else {
      unset($this->_iId);
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/gallery');
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
      $this->_oSmarty->assign('content', \CandyCMS\Model\Gallery::getFileContent($this->_iId));

      # Language
      $this->_oSmarty->assign('lang_headline', LANG_GALLERY_FILE_UPDATE_TITLE);
    }
    # Create
    else {
      # See helper/Image.helper.php for details!
      # r = resize, c = cut
      $sDefault = isset($this->_aRequest['cut']) ? \CandyCMS\Helper\Helper::formatInput($this->_aRequest['cut']) : 'c';

      $this->_oSmarty->assign('default', $sDefault);
      $this->_oSmarty->assign('content', isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '');

      # Language
      $this->_oSmarty->assign('lang_create_file_cut', LANG_GALLERY_FILE_CREATE_LABEL_CUT);
      $this->_oSmarty->assign('lang_create_file_resize', LANG_GALLERY_FILE_CREATE_LABEL_RESIZE);
      $this->_oSmarty->assign('lang_file_choose', LANG_GALLERY_FILE_CREATE_LABEL_CHOOSE);
      $this->_oSmarty->assign('lang_headline', LANG_GALLERY_FILE_CREATE_TITLE);
    }

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    $this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('galleries', '_form_file');
    return $this->_oSmarty->fetch('_form_file.tpl');
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
    if (USER_RIGHT < 3)
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);

    else {
      if (isset($this->_aRequest['createfile_gallery'])) {
        if ($this->_createFile() === true) {
          # Log uploaded image. Request ID = album id
          \CandyCMS\Controller\Log::insert($this->_aRequest['section'], 'createfile', (int) $this->_aRequest['id']);
          return \CandyCMS\Helper\Helper::successMessage(LANG_GALLERY_FILE_CREATE_SUCCESS, '/gallery/' . $this->_iId);
        }
        else
          return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_UPLOAD_CREATE, '/gallery/' . $this->_iId . '/createfile');
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
    if (isset($this->_aFile['file']) && !empty($this->_aFile['file']['name'][0])) {

      for ($iI = 0; $iI < count($this->_aFile['file']['name']); $iI++) {
        $aFile['name'] = $this->_aFile['file']['name'][$iI];
        $aFile['type'] = $this->_aFile['file']['type'][$iI];
        $aFile['tmp_name'] = $this->_aFile['file']['tmp_name'][$iI];
        $aFile['error'] = $this->_aFile['file']['error'][$iI];
        $aFile['size'] = $this->_aFile['file']['size'][$iI];

        $this->_oModel->createFile($aFile);
      }

      return true;
    }
    else {
      $this->_aError['file'] = LANG_ERROR_FORM_MISSING_FILE;
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
    if( USER_RIGHT < 3 )
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/gallery');

    else {
      if( isset($this->_aRequest['updatefile_gallery']) ) {
        if( $this->_oModel->updateFile($this->_iId) === true) {
          \CandyCMS\Controller\Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
          return \CandyCMS\Helper\Helper::successMessage(LANG_SUCCESS_UPDATE, '/gallery');
        }
        else
          return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_GLOBAL, '/gallery');
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
    if( USER_RIGHT < 3 )
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/gallery');

    else {
      if($this->_oModel->destroyFile($this->_iId) === true) {
        \CandyCMS\Controller\Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId);
        unset($this->_iId);
        return \CandyCMS\Helper\Helper::successMessage(LANG_SUCCESS_DESTROY, '/gallery');
      }
      else
        return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_GLOBAL_FILE_COULD_NOT_BE_DESTROYED, '/gallery');
    }
  }
}
<?php

/**
 * This class provides an overview about avaiable downloads, counts them and gives
 * administrators and moderators the option to upload and manage files.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Download as Model;

require_once 'app/models/Download.model.php';

class Download extends Main {

  /**
   * Include the download model.
   *
   * @access public
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init() {
    $this->_oModel = new Model($this->_aRequest, $this->_aSession, $this->_aFile);
  }

  /**
   * Download entry or show download overview (depends on a given ID or not).
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    $this->_aData = $this->_oModel->getData($this->_iId);

    # Direct download for this id
    if (!empty($this->_iId)) {
      $sFile = $this->_aData['file'];

      # Update download count
      Model::updateDownloadCount($this->_iId);

      # Get mime type
      if(function_exists('finfo_open')) {
        $oInfo = finfo_open(FILEINFO_MIME_TYPE);
        $sMimeType = finfo_file($oInfo, PATH_UPLOAD . '/download/' . $sFile);
        header('Content-type: ' . $sMimeType);
      }

      # Send file directly
      header('Content-Disposition: attachment; filename="' . $sFile . '"');
      readfile(PATH_UPLOAD . '/download/' . $sFile);

      # No more actions down here
      exit();
    }
    # Overview
    else {
      $this->oSmarty->assign('download', $this->_aData);

      $this->oSmarty->template_dir = Helper::getTemplateDir('downloads', 'show');
      return $this->oSmarty->fetch('show.tpl');
    }
  }

  /**
   * Build form template to create or update a download entry.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormTemplate() {
    # Update
    if (!empty($this->_iId))
      $this->_aData = $this->_oModel->getData($this->_iId, true);

    # Create
    else {
      $this->oSmarty->assign('_action_url_', '/download/create');
      $this->oSmarty->assign('_formdata_', 'create_download');

      $this->_aData['category']   = isset($this->_aRequest['category']) ? $this->_aRequest['category'] : '';
      $this->_aData['content']    = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
      $this->_aData['title']      = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
    }

    foreach ($this->_aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->oSmarty->assign('error_' . $sField, $sMessage);
    }

    $this->oSmarty->template_dir = Helper::getTemplateDir('downloads', '_form');
    return $this->oSmarty->fetch('_form.tpl');
  }

  /**
   * Create a download entry.
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
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('downloads'));
      return Helper::successMessage($this->oI18n->get('success.create'), '/download');
    }
    else
      return Helper::errorMessage($this->oI18n->get('error.sql'), '/download');
  }

  /**
   * Update a download entry.
   *
   * Activate model, insert data into the database and redirect afterwards.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _update() {
    $this->_setError('title');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id']);
      return Helper::successMessage($this->oI18n->get('success.update'), '/download');
    }
    else
      return Helper::errorMessage($this->oI18n->get('error.sql'), '/download');
  }

  /**
   * Delete a download entry.
   *
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id']);
      return Helper::successMessage($this->oI18n->get('success.destroy'), '/download');
    }
    else
      return Helper::errorMessage($this->oI18n->get('error.sql'), '/download');
  }
}
<?php

/**
 * This class provides an overview about available downloads, counts them and gives
 * administrators and moderators the option to upload and manage files.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Core\Controllers;

use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\I18n;
use CandyCMS\Core\Helpers\Upload;

class Downloads extends Main {

  /**
   * Download entry or show download overview (depends on a given ID or not).
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    if ($this->_iId) {
      $sFile = $this->_oModel->getFileName($this->_iId);

      # if file not found, redirect user to overview
      if (!$sFile)
        Helper::redirectTo ('/errors/404');

      # Update download count
      $this->_oModel->updateDownloadCount($this->_iId);

      # Get mime type
      if(function_exists('finfo_open')) {
        $sMimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE),
              	Helper::removeSlash(PATH_UPLOAD . '/' . $this->_aRequest['controller'] . '/' . $sFile));
        header('Content-type: ' . $sMimeType);
      }

      # Send file directly
      header('Content-Disposition: attachment; filename="' . $sFile . '"');
      exit(readfile(Helper::removeSlash(PATH_UPLOAD . '/' . $this->_aRequest['controller'] . '/' . $sFile)));
    }
    else {
      $sTemplateDir	  = Helper::getTemplateDir($this->_aRequest['controller'], 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

    	if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID))
        $this->oSmarty->assign('downloads', $this->_oModel->getData($this->_iId));

      $this->oSmarty->setTemplateDir($sTemplateDir);
    	return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
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
    $sTemplateDir	  = Helper::getTemplateDir($this->_aRequest['controller'], '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

    # Update
    if ($this->_iId)
      $aData = $this->_oModel->getData($this->_iId, true);

    # Create
    else {
      $aData['category']   = isset($this->_aRequest['category']) ? $this->_aRequest['category'] : '';
      $aData['content']    = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
      $aData['title']      = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
    }

    $this->oSmarty->assign('_categories_', $this->_oModel->getTypeaheadData('downloads', 'category'));

    foreach ($aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
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
    $this->_setError('category');
    $this->_setError('file');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    else {
    	require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/Upload.helper.php';

      # Set up upload helper and rename file to title
      $oUploadFile = new Upload($this->_aRequest,
                                $this->_aSession, $this->_aFile,
                              	Helper::formatInput($this->_aRequest['title']));

      # File is up so insert data into database
      $aRetVals = $oUploadFile->uploadFiles('downloads');
      if ($aRetVals[0] === true) {
        $this->oSmarty->clearCacheForController($this->_aRequest['controller']);
        $this->oSmarty->clearCacheForController('searches');

        $aIds = $oUploadFile->getIds(false);
        $aExts = $oUploadFile->getExtensions();

        if ($this->_oModel->create($aIds[0] . '.' . $aExts[0], $aExts[0]) === true) {
          Logs::insert($this->_aRequest['controller'],
                      $this->_aRequest['action'],
                      $this->_oModel->getLastInsertId('downloads'),
                      $this->_aSession['user']['id']);

          return Helper::successMessage(I18n::get('success.create'), '/' . $this->_aRequest['controller']);
        }
        else
          return Helper::errorMessage(I18n::get('error.sql'), '/' . $this->_aRequest['controller']);
      }
      else
        return Helper::errorMessage(I18n::get('error.missing.file'), '/' . $this->_aRequest['controller']);
    }
  }

  /**
   * Update a download entry.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
	protected function _update() {
    return parent::_update('searches');
  }

  /**
   * Destroy a download entry.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
	protected function _destroy() {
    return parent::_destroy('searches');
  }

}
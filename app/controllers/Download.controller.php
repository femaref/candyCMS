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

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Helper\Upload as Upload;
use CandyCMS\Model\Download as Model;
use Smarty;

class Download extends Main {

  /**
   * Download entry or show download overview (depends on a given ID or not).
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    # Direct download for this id
    if (!empty($this->_iId)) {
      $sFile = & $this->_oModel->getFileName($this->_iId);

      # Update download count
      $this->_oModel->updateDownloadCount($this->_iId);

      # Get mime type
      if(function_exists('finfo_open')) {
        $oInfo = finfo_open(FILEINFO_MIME_TYPE);
        $sMimeType = finfo_file($oInfo, Helper::removeSlash(PATH_UPLOAD . '/download/' . $sFile));
        header('Content-type: ' . $sMimeType);
      }

      # Send file directly
      header('Content-Disposition: attachment; filename="' . $sFile . '"');
      readfile(Helper::removeSlash(PATH_UPLOAD . '/download/' . $sFile));

      # No more actions down here
      exit();
    }
    # Overview
    else {
			$sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'show');
			$sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

			$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
			$this->oSmarty->setTemplateDir($sTemplateDir);

			if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID))
				$this->oSmarty->assign('download', $this->_oModel->getData($this->_iId));

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
    # Update
    if (!empty($this->_iId))
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

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

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

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    else {
			require_once PATH_STANDARD . '/app/helpers/Upload.helper.php';

      # Set up upload helper and rename file to title
      $oUploadFile = new Upload($this->_aRequest,
																$this->_aSession, $this->_aFile,
																Helper::formatInput($this->_aRequest['title']));

      # File is up so insert data into database
      if ($oUploadFile->uploadFile('download') === true) {
				$this->oSmarty->clearCache(null, $this->_aRequest['controller']);

        if ($this->_oModel->create($oUploadFile->getId(false), $oUploadFile->getExtension()) === true) {
          Log::insert($this->_aRequest['controller'],
											$this->_aRequest['action'],
											$this->_oModel->getLastInsertId('downloads'),
											$this->_aSession['user']['id']);

          return Helper::successMessage(I18n::get('success.create'), '/download');
        }
        else
          return Helper::errorMessage(I18n::get('error.sql'), '/download');
      }
      else
        return Helper::errorMessage(I18n::get('error.missing.file'), '/download');
    }
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
			$this->oSmarty->clearCache(null, $this->_aRequest['controller']);

      Log::insert($this->_aRequest['controller'],
									$this->_aRequest['action'],
									(int) $this->_aRequest['id'],
									$this->_aSession['user']['id']);

      return Helper::successMessage(I18n::get('success.update'), '/download');
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), '/download');
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
			$this->oSmarty->clearCache(null, $this->_aRequest['controller']);

      Log::insert($this->_aRequest['controller'],
									$this->_aRequest['action'],
									(int) $this->_aRequest['id'],
									$this->_aSession['user']['id']);

      return Helper::successMessage(I18n::get('success.destroy'), '/download');
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), '/download');
  }
}
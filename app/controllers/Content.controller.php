<?php

/**
 * CRUD action of content entries.
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
use CandyCMS\Model\Content as Model;
use Smarty;

class Content extends Main {

  /**
   * Include the content model.
   *
   * @access public
   *
   */
  public function __init() {
    require PATH_STANDARD . '/app/models/Content.model.php';
    $this->_oModel = new Model($this->_aRequest, $this->_aSession);
  }

  /**
   * Show content entry or content overview (depends on a given ID or not).
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    if (empty($this->_iId)) {
      $this->_setTitle(I18n::get('global.manager.content'));

      $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'overview');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'overview');

      $this->oSmarty->setTemplateDir($sTemplateDir);
      $this->oSmarty->assign('content', $this->_oModel->getData($this->_iId));

      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
    else {
      $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

      $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
			$this->oSmarty->setCacheLifetime(300);
			$this->oSmarty->setTemplateDir($sTemplateDir);

			if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
				$aData = & $this->_oModel->getData($this->_iId);
				$this->oSmarty->assign('content', $aData);

				if (!empty($aData)) {
					$this->_setDescription($aData[$this->_iId]['teaser']);
					$this->_setKeywords($aData[$this->_iId]['keywords']);
					$this->_setTitle($this->_removeHighlight($aData[$this->_iId]['title']));
				}
				else
					Helper::redirectTo('/error/404');
			}

			return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
		}
  }

  /**
   * Build form template to create or update a content entry.
   * INFO: $this->_setTitle comes from section helper.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormTemplate() {
    if (!empty($this->_iId)) {
      $aData = $this->_oModel->getData($this->_iId, true);
      $this->_setTitle($aData['title']);
    }
    else {
      $aData['title']			= isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
      $aData['teaser']		= isset($this->_aRequest['teaser']) ? $this->_aRequest['teaser'] : '';
      $aData['keywords']	= isset($this->_aRequest['keywords']) ? $this->_aRequest['keywords'] : '';
      $aData['content']		= isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
      $aData['published']	= isset($this->_aRequest['published']) ? $this->_aRequest['published'] : '';
    }

    foreach($aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Create a content entry.
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
    $this->_setError('content');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->create() === true) {
      Log::insert($this->_aRequest['section'],
									$this->_aRequest['action'],
									$this->_oModel->getLastInsertId('contents'),
									$this->_aSession['userdata']['id']);

      return Helper::successMessage(I18n::get('success.create'), '/content');
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), '/content');
  }

  /**
   * Activate model, insert data into the database and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _update() {
    $this->_setError('title');
    $this->_setError('content');

    $sRedirect = '/content/' . (int) $this->_aRequest['id'];

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
			$this->oSmarty->clearCache(null, $this->_aRequest['section']);

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
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
			$this->oSmarty->clearCache(null, $this->_aRequest['section']);

      Log::insert($this->_aRequest['section'],
							$this->_aRequest['action'],
							(int) $this->_aRequest['id'],
							$this->_aSession['userdata']['id']);

      return Helper::successMessage(I18n::get('success.destroy'), '/content');
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), '/content');
  }
}
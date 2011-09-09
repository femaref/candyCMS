<?php

/**
 * CRUD action of content entries.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Content as Model;

require_once 'app/models/Content.model.php';

class Content extends Main {

  /**
   * Include the content model.
   *
   * @access public
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init() {
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
    $this->_aData = $this->_oModel->getData($this->_iId);
    $this->_oSmarty->assign('content', $this->_aData);

    if (empty($this->_iId)) {
      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_CONTENTMANAGER);
      $this->_setTitle(LANG_GLOBAL_CONTENTMANAGER);

      $this->_oSmarty->template_dir = Helper::getTemplateDir('contents', 'overview');
      return $this->_oSmarty->fetch('overview.tpl');
    }
    else {
      $this->_setDescription($this->_aData[$this->_iId]['teaser']);
      $this->_setKeywords($this->_aData[$this->_iId]['keywords']);
      $this->_setTitle($this->_removeHighlight($this->_aData[$this->_iId]['title']));

      $this->_oSmarty->template_dir = Helper::getTemplateDir('contents', 'show');
      return $this->_oSmarty->fetch('show.tpl');
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
    # Update
    if (!empty($this->_iId)) {
      $this->_aData = $this->_oModel->getData($this->_iId, true);

      # Language
      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
      $this->_oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);

      $this->_setTitle(Helper::removeSlahes($this->_aData['title']));
    }
    # Create
    else {
      $this->_aData['title']    = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
      $this->_aData['teaser']   = isset($this->_aRequest['teaser']) ? $this->_aRequest['teaser'] : '';
      $this->_aData['keywords'] = isset($this->_aRequest['keywords']) ? $this->_aRequest['keywords'] : '';
      $this->_aData['content']  = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';

      # Language
      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_CREATE_ENTRY);
      $this->_oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);
    }

    foreach($this->_aData as $sColumn => $sData)
      $this->_oSmarty->assign($sColumn, $sData);

    if (!empty($this->_aError)) {
      foreach ($this->_aError as $sField => $sMessage)
        $this->_oSmarty->assign('error_' . $sField, $sMessage);
    }

    $this->_oSmarty->assign('lang_create_keywords_info', LANG_CONTENT_INFO_KEYWORDS);
    $this->_oSmarty->assign('lang_create_teaser_info', LANG_CONTENT_INFO_TEASER);

    $this->_oSmarty->template_dir = Helper::getTemplateDir('contents' ,'_form');
    return $this->_oSmarty->fetch('_form.tpl');
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
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('contents'));
      return Helper::successMessage(LANG_SUCCESS_CREATE, '/content');
    }
    else
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/content');
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
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id']);
      return Helper::successMessage(LANG_SUCCESS_UPDATE, $sRedirect);
    }
    else
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, $sRedirect);
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
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'],  (int) $this->_aRequest['id']);
      return Helper::successMessage(LANG_SUCCESS_DESTROY, '/content');
    }
    else
      return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/content');
  }
}
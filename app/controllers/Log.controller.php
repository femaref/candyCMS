<?php

/**
 * CRUD actions of logs.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Controller;

require_once 'app/models/Log.model.php';
require_once 'app/helpers/Page.helper.php';

class Log extends Main {

  /**
   * Include the log model.
   *
   * @access public
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init() {
    $this->_oModel = new \CandyCMS\Model\Log($this->_aRequest, $this->_aSession);
  }

  /**
   * Show log overview if we have admin rights.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    if (USER_RIGHT < 4)
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/');

    else {
      $this->_oSmarty->assign('logs', $this->_oModel->getData());

      # Do we need pages?
      $this->_oSmarty->assign('_pages_', $this->_oModel->oPage->showPages('/log'));

      # Language
      $this->_oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
      $this->_oSmarty->assign('lang_headline', LANG_GLOBAL_LOGS);

      $this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('logs', 'show');
      return $this->_oSmarty->fetch('show.tpl');
    }
  }

  /**
   * Show content entry or content overview (depends on a given ID or not).
   *
   * @static
   * @access public
   * @param string $sSectionName name of section
   * @param string $sActionName name of action (CRUD)
   * @param integer $iActionId ID of the row that is affected
   * @param integer $iUserId ID of the acting user
   * @param integer $iTimeStart starting timestamp of the entry
   * @param integer $iTimeEnd ending timestamp of the entry
   * @return boolean status of query
   *
   */
  public static function insert($sSectionName, $sActionName, $iActionId = 0, $iUserId = USER_ID, $iTimeStart = '', $iTimeEnd = '') {
    return \CandyCMS\Model\Log::insert($sSectionName, $sActionName, $iActionId, $iUserId, $iTimeStart, $iTimeEnd);
  }

  /**
   * Delete entry if we have enough rights.
   * We must override the main method due to a diffent required user right.
   *
   * @access public
   * @return boolean status of model action.
   * @override app/controllers/Main.controller.php
   *
   */
  public function destroy() {
    return (USER_RIGHT < 4) ? \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION, '/') : $this->_destroy();
  }

  /**
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access protected
   * @return boolean status of model action.
   *
   */
  protected function _destroy() {
    if ($this->_oModel->destroy($this->_iId) === true) {
      \CandyCMS\Controller\Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $this->_iId);
      return \CandyCMS\Helper\Helper::successMessage(LANG_SUCCESS_DESTROY, '/log');
    }
    else {
      unset($this->_iId);
      return \CandyCMS\Helper\Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/log');
    }
  }
}
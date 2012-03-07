<?php

/**
 * CRUD actions of logs.
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
use CandyCMS\Model\Log as Model;

require_once PATH_STANDARD . '/app/models/Log.model.php';

class Log extends Main {

  /**
   * Include the log model.
   *
   * @access public
   *
   */
  public function __init() {
    $this->_oModel = new Model($this->_aRequest, $this->_aSession);
  }

  /**
   * Show log overview if we have admin rights.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    if ($this->_aSession['userdata']['role'] < 4)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

    else {
      $this->oSmarty->assign('logs', $this->_oModel->getData());
      $this->oSmarty->assign('_pages_', $this->_oModel->oPagination->showPages());

      $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
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
  public static function insert($sSectionName, $sActionName, $iActionId = 0, $iUserId = 0, $iTimeStart = '', $iTimeEnd = '') {
    return Model::insert($sSectionName, $sActionName, $iActionId, $iUserId, $iTimeStart, $iTimeEnd);
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
      Log::insert($this->_aRequest['section'],
									$this->_aRequest['action'],
									$this->_iId,
									$this->_aSession['userdata']['id']);

      return Helper::successMessage(I18n::get('success.destroy'), '/log');
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), '/log');
  }
}
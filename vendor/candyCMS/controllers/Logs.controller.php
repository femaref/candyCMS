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

namespace CandyCMS\Core\Controller;

use CandyCMS\Core\Helper\Helper as Helper;
use CandyCMS\Core\Helper\I18n as I18n;

class Logs extends Main {

  /**
   * Show log overview if we have admin rights.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    if ($this->_aSession['user']['role'] < 4)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

    else {
      $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

      $this->oSmarty->assign('logs', $this->_oModel->getData());
      $this->oSmarty->assign('_pages_', $this->_oModel->oPagination->showPages('/' . $this->_aRequest['controller']));

      $this->setTitle(I18n::get('global.logs'));
      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
  }

  /**
   * Create a new Log-Entry
   *
   * @static
   * @access public
   * @param string $sControllerName name of controller
   * @param string $sActionName name of action (CRUD)
   * @param integer $iActionId ID of the row that is affected
   * @param integer $iUserId ID of the acting user
   * @param integer $iTimeStart starting timestamp of the entry
   * @param integer $iTimeEnd ending timestamp of the entry
   * @return boolean status of query
   * @todo extension check
   *
   */
  public static function insert($sControllerName, $sActionName, $iActionId = 0, $iUserId = 0, $iTimeStart = '', $iTimeEnd = '') {
    require_once PATH_STANDARD . '/vendor/candyCMS/models/Logs.model.php';

    $bReturn = \CandyCMS\Core\Model\Logs::insert($sControllerName, $sActionName, $iActionId, $iUserId, $iTimeStart, $iTimeEnd);

    if ($bReturn)
      \CandyCMS\Core\Helper\SmartySingleton::getInstance()->clearCacheForController('logs');

    return $bReturn;
  }

  /**
   * There is no create Action for the sitemaps Controller
   *
   * @access public
   *
   */
  public function create() {
    Helper::redirectTo('/errors/404');
  }

  /**
   * There is no update Action for the sitemaps Controller
   *
   * @access public
   *
   */
  public function update() {
    Helper::redirectTo('/errors/404');
  }

  /**
   * Update the Endtime of some LogEntry
   *
   * @static
   * @param integer $iLogsId id of log entry to update
   * @param integer $iEndTime the new Timestamp
   * @return boolean status of query
   * @todo extension check
   *
   */
  public static function updateEndTime($iLogsId, $iEndTime = null) {
    require_once PATH_STANDARD . '/vendor/candyCMS/models/Logs.model.php';

    if ($iEndTime == null)
      $iEndTime = time();

    $bReturn = \CandyCMS\Core\Model\Logs::setEndTime($iLogsId, $iEndTime);

    if ($bReturn)
      \CandyCMS\Core\Helper\SmartySingleton::getInstance()->clearCacheForController('logs');

    return $bReturn;
  }
}
<?php

/**
 * CRUD action of simple calendar.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Calendar as Model;

require_once 'app/models/Calendar.model.php';

class Calendar extends Main {

  /**
   * Include the calendar model.
   *
   * @access public
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init() {
    $this->_oModel = new Model($this->_aRequest, $this->_aSession);
  }

  /**
   * Show calendar overview.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    $this->_aData = $this->_oModel->getData($this->_iId);
		$this->oSmarty->assign('calendar', $this->_aData);

		$sTemplateDir = Helper::getTemplateDir('calendars', 'show');
		$this->oSmarty->template_dir = $sTemplateDir;
		return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'show'));
  }
}
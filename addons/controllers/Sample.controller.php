<?php

/**
 * This is an example how to create a single addon.
 * Properties were set before in Addon.helper.php
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Addon\Controller;

use CandyCMS\Helper\Helper as Helper;

require_once 'addons/models/Sample.model.php';

class Addon_Sample extends \CandyCMS\Controller\Main {

  /**
   * Method to include the model files and start action beside the constructor.
   *
   * @access public
   *
   */
  public function __init() {
    $this->_oModel = new \CandyCMS\Addon\Model\Addon_Sample($this->_aRequest, $this->_aSession, $this->_aFile);
  }

  /**
   * Return the content.
   *
   * @access public
   * @return string example content.
   *
   */
  public function show() {
		$sTemplateDir		= Helper::getTemplateDir('samples', 'show');
		$sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

    $this->setTitle('Sample addon');

		$this->oSmarty->setTemplateDir($sTemplateDir);
		return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   *
   * @access protected
   * @param boolean $bUpdate
   *
   */
  protected function _showFormTemplate($bUpdate = true) {

  }

  /**
   *
   * @access protected
   *
   */
  protected function _create() {

  }

  /**
   *
   * @access protected
   *
   */
  protected function _update() {

  }

  /**
   *
   * @access protected
   *
   */
  protected function _destroy() {

  }
}
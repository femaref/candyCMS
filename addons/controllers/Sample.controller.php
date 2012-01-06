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
	 * Initialize the controller by adding input params, set default id and start template engine.
	 *
	 * @access public
	 * @param array $aRequest alias for the combination of $_GET and $_POST
	 * @param array $aSession alias for $_SESSION
	 * @param array $aFile alias for $_FILE
   *
   */
  public function __construct($aRequest, $aSession, $aFile = '') {
    $this->_aRequest  = & $aRequest;
    $this->_aSession  = & $aSession;
    $this->_aFile     = & $aFile;
  }

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
   * @todo
   *
   */
  public function show() {
    #$sTemplateDir = Helper::getTemplateDir('samples', 'show');
    #$this->oSmarty->template_dir = $sTemplateDir;
    #return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'samples'));
  }

  /**
   *
   * @access protected
   * @param type $bUpdate
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
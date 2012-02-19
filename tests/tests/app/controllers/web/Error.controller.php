<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

require_once PATH_STANDARD . '/app/controllers/Error.controller.php';
require_once PATH_STANDARD . '/app/helpers/Helper.helper.php';

use \CandyCMS\Controller\Error as Error;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfErrorController extends WebTestCase {

	public $oObject;
	protected $_aRequest;
	protected $_aSession;
	protected $_aFile;
	protected $_aCookie;

	function setUp() {
		$this->_aRequest	= array('section' => 'error');
		$this->_aFile			= array();
		$this->_aCookie		= array();
		$this->_aSession['userdata'] = array(
				'email' => '',
				'facebook_id' => '',
				'id' => 0,
				'name' => '',
				'surname' => '',
				'password' => '',
				'role' => 0,
				'full_name' => ''
		);

		$this->oObject = new Error($this->_aRequest, $this->_aSession);
	}

	function tearDown() {
		parent::tearDown();
		unset($this->_aRequest, $this->_aFile, $this->_aCookie, $this->_aSession['userdata']);
	}

	function testShow404AsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL . '/error/404'));
		$this->assertText(I18n::get('error.404.title'));
	}
}
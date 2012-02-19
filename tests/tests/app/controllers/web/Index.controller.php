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

require_once PATH_STANDARD . '/app/controllers/Index.controller.php';

use \CandyCMS\Controller\Index as Index;

class WebTestOfIndexController extends WebTestCase {

	public $oObject;
	protected $_aRequest;
	protected $_aSession;
	protected $_aFile;
	protected $_aCookie;

	function setUp() {
		$this->_aRequest	= array();
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

		$this->oObject = new Index($this->_aRequest, $this->_aSession);
	}

	function tearDown() {
		parent::tearDown();
		unset($this->_aRequest, $this->_aFile, $this->_aCookie, $this->_aSession['userdata']);
	}

	function testShowIndexAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL));
		$this->assertText('Login'); # This should be on every page.
	}
}
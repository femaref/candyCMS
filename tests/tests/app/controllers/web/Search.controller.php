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

require_once PATH_STANDARD . '/app/controllers/Search.controller.php';

use \CandyCMS\Controller\Search as Search;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfSearchController extends WebTestCase {

	public $oObject;
	protected $_aRequest;
	protected $_aSession;
	protected $_aFile;
	protected $_aCookie;

	function setUp() {
		$this->_aRequest	= array('section' => 'search');
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

		$this->oObject = new Search($this->_aRequest, $this->_aSession);
	}

	function tearDown() {
		parent::tearDown();
		unset($this->_aRequest, $this->_aFile, $this->_aCookie, $this->_aSession['userdata']);
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->_aRequest['section']));
		$this->assertResponse(200);
		$this->assertText(I18n::get('global.search'));

		$this->post(WEBSITE_URL . '/' . $this->_aRequest['section'], array(
				'search' => md5(RANDOM_HASH)
		));

		$this->assertText(md5(RANDOM_HASH));
		$this->assertResponse(200);
	}
}
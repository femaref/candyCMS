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

require_once PATH_STANDARD . '/app/models/Search.model.php';

use \CandyCMS\Model\Search as Search;

class TestOfSearchModel extends UnitTestCase {

	public $oObject;
	protected $_aRequest;
	protected $_aSession;

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

	/**
	 * Tests if query run through. We use only the blogs table.
	 */
  function testGetData() {
		$aData = $this->oObject->getData(md5(RANDOM_HASH), array('blogs'));
    $this->assertIsA($aData['blogs'], 'array');
  }
}
<?php

/**
 * PHP unit tests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

require_once PATH_STANDARD . '/app/controllers/Index.controller.php';

use \CandyCMS\Controller\Index as Index;

class UnitTestOfIndexController extends UnitTestCase {

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

	function testGetConfigFiles() {
		$this->assertTrue(file_exists(PATH_STANDARD . '/config/Candy.inc.php'), 'Candy.inc.php exists.');
		$this->assertTrue(file_exists(PATH_STANDARD . '/config/Plugins.inc.php'), 'Plugins.inc.php exists.');
		$this->assertTrue(file_exists(PATH_STANDARD . '/config/Mailchimp.inc.php'), 'Plugins.inc.php exists.');
		$this->assertTrue($this->oObject->getConfigFiles(array('Candy', 'Plugins', 'Mailchimp')));
	}

	function testGetPlugins() {
		$this->assertTrue($this->oObject->getPlugins(ALLOW_PLUGINS));
	}

	function testGetLanguage() {
		$this->assertIsA($this->oObject->getLanguage(), 'string');
	}

	function testSetTemplate() {
		$this->assertIsA($this->oObject->setTemplate(), 'string');
	}

	function testDirIsWritable() {
		$sFile = PATH_STANDARD . '/upload/temp/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile), 'Upload folder is basically writeable.');
		@unlink($sFile);
	}
}
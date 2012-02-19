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

require_once PATH_STANDARD . '/app/controllers/Download.controller.php';

use \CandyCMS\Controller\Download as Download;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfDownloadController extends WebTestCase {

	public $oObject;
	protected $_aRequest;
	protected $_aSession;
	protected $_aFile;
	protected $_aCookie;

	function setUp() {
		$this->_aRequest	= array('section' => 'download');
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

		$this->oObject = new Download($this->_aRequest, $this->_aSession);
	}

	function tearDown() {
		parent::tearDown();
		unset($this->_aRequest, $this->_aFile, $this->_aCookie, $this->_aSession['userdata']);
	}

	function testShowDownloadsAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL . '/download'));
		$this->assertText('KB'); # There must be at leastonea file with KB ending.
	}

	/*function testShowDownloadsAsModerator() {
		$this->_aSession['userdata']['id']		= 3;
		$this->_aSession['userdata']['role']	= 3;

		$this->assertTrue($this->get(WEBSITE_URL . '/download'));
		$this->assertText(I18n::get('global.create.entry'));
	}*/

	function testUpdateDownloadAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL . '/download/1/update'));
		$this->assertText(I18n::get('error.missing.permission'));
	}

	function testDestroyAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL . '/download/1/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
	}

	function testDirIsWritable() {
		$sFile = PATH_STANDARD . '/upload/download/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile), 'Download folder is writeable.');
		@unlink($sFile);
	}
}
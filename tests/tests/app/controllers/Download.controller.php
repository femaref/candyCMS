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

class WebTestOfDownloadController extends CandyWebTest {

	function setUp() {
		$this->aRequest['section'] = 'download';
		$this->oObject = new Download($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShowDownloadsAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section']));
		$this->assertResponse(200);
		$this->assertText('098dec456d');
	}

	/*function testShowDownloadsAsModerator() {
		$this->_aSession['userdata']['id']		= 3;
		$this->_aSession['userdata']['role']	= 3;

		$this->assertTrue($this->get(WEBSITE_URL . '/download'));
		$this->assertText(I18n::get('global.create.entry'));
	}*/

	function testUpdateDownloadAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/1/update'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testDestroyAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section'] . '/1/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testDirIsWritable() {
		$sFile = $this->createFile('upload/' . $this->aRequest['section']);
		$this->assertTrue(file_exists($sFile), 'Download folder is writeable.');
		unlink($sFile);
	}
}
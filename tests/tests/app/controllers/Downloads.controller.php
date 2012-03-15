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

require_once PATH_STANDARD . '/app/controllers/Downloads.controller.php';

use \CandyCMS\Controller\Downloads as Downloads;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfDownloadController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'downloads';
		$this->oObject = new Downloads($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText('098dec456d');
	}

	function testCreateDownload() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testUpdateDownload() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testDestroyDownload() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
    $this->assertResponse(200);
	}

	function testDirIsWritable() {
		$this->assertTrue(parent::createFile('upload/' . $this->aRequest['controller']));
		$this->assertTrue(parent::removeFile('upload/' . $this->aRequest['controller']));
	}
}
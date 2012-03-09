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

require_once PATH_STANDARD . '/app/controllers/Main.controller.php';
require_once PATH_STANDARD . '/app/controllers/Media.controller.php';

use \CandyCMS\Controller\Media as Media;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfMediaController extends CandyWebTest {

	function setUp() {
		$this->aRequest['controller'] = 'media';
		$this->oObject = new Media($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

  function testShow() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertText(I18n::get('error.missing.permission'));
		$this->assertResponse('200');
	}

	function testCreate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
		$this->assertText(I18n::get('error.missing.permission'));
		$this->assertResponse('200');
	}

	function testUpdate() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/update'));
		$this->assertText(I18n::get('error.missing.permission'));
		$this->assertResponse('200');
	}

	function testDestroy() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/destroy'));
		$this->assertText(I18n::get('error.missing.permission'));
		$this->assertResponse('200');
	}

  function testDirIsWritable() {
		$this->assertTrue(parent::createFile('upload/' . $this->aRequest['controller']));
		$this->assertTrue(parent::removeFile('upload/' . $this->aRequest['controller']));
	}
}
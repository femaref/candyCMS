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

require_once PATH_STANDARD . '/app/controllers/Gallery.controller.php';

use \CandyCMS\Controller\Gallery as Gallery;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfGalleryController extends CandyWebTest {

	function setUp() {
		$this->aRequest['section'] = 'gallery';
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShowGalleryAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['section']));
		$this->assertResponse(200);

		# Todo: Something
		$this->assertText(I18n::get('global.files'), 'There is no file.');
	}

	function testDirIsWritable() {
		$sFile = PATH_STANDARD . '/upload/' . $this->aRequest['section'] . '/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile), 'Gallery folder is writeable.');
		@unlink($sFile);
	}
}
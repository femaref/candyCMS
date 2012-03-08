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
		$this->aRequest['controller'] = 'gallery';
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShowGallery() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
		$this->assertResponse(200);
		$this->assertText('6dffc4c552');
	}

	function testShowAlbum() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/6dffc4c552'));
		$this->assertResponse(200);
		$this->assertText('982e960e18');
	}

	function testShowImage() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/image/1'));
		$this->assertResponse(200);
		$this->assertText('782c660e17');
	}

	function testDirIsWritable() {
		$sFile = PATH_STANDARD . '/upload/' . $this->aRequest['controller'] . '/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile), 'Gallery folder is writeable.');
		@unlink($sFile);
	}
}
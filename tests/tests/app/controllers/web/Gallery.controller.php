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

class WebTestOfGalleryController extends WebTestCase {

	function testDirIsWritable() {
		$sFile = PATH_STANDARD . '/upload/gallery/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile), 'Gallery folder is writeable.');
		@unlink($sFile);
	}
}
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

require_once PATH_STANDARD . '/app/controllers/Index.controller.php';

use \CandyCMS\Controller\Index as Index;

class UnitTestOfIndexController extends CandyUnitTest {

	function setUp() {
		$this->oObject = new Index($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
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

	function testUploadDirIsWritable() {
		$sFile = PATH_STANDARD . '/upload/temp/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Upload folder is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile), 'Upload folder is basically writeable.');
		@unlink($sFile);
	}

	function testCacheDirIsWritable() {
		$sFile = PATH_STANDARD . '/cache/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Cache is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile), 'Cache folder is writeable.');
		@unlink($sFile);
	}

	function testCompileDirIsWritable() {
		$sFile = PATH_STANDARD . '/compile/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Compile dir is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile), 'Compile folder is writeable.');
		@unlink($sFile);
	}

	function testBackupDirIsWritable() {
		$sFile = PATH_STANDARD . '/backup/test.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Backup dir is writeable.' . "\n");
		fclose($oFile);

		$this->assertTrue(file_exists($sFile), 'Backup folder is writeable.');
		@unlink($sFile);
	}
}

class WebTestOfIndexController extends CandyWebTest {

	function setUp() {
		$this->oObject = new Index($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShowIndexAsGuest() {
		$this->assertTrue($this->get(WEBSITE_URL));
		$this->assertResponse(200);
		$this->assertText('Login'); # This should be on every page.
	}

	function testShow404() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . md5(RANDOM_HASH)));
		$this->assertResponse(404);
	}

	function testShowSampleAddon() {
		$this->assertTrue($this->get(WEBSITE_URL . '/sample'));
		$this->assertResponse(200);
		$this->assertText('Sample');
		$this->assertNoText('Error');
	}
}
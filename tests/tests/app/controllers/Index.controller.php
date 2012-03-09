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
require_once PATH_STANDARD . '/app/models/Session.model.php';

use \CandyCMS\Controller\Index as Index;
use \CandyCMS\Helper\I18n as I18n;

class UnitTestOfIndexController extends CandyUnitTest {

	function setUp() {
		$this->oObject = new Index($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
    $this->oObject->__destruct();
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

	function testGetRoutes() {
		$this->assertIsA($this->oObject->getRoutes(), 'array');
	}

	function testGetLanguage() {
    $this->assertEqual($this->oObject->getLanguage(), WEBSITE_LOCALE);
	}

  /**
   *@todo
   */
  function testGetCronjob() {

  }

  function testGetFacebookExtension() {
    $this->assertFalse($this->oObject->getFacebookExtension());
  }

  /**
   *@todo
   */
  function testSetUser() {

  }

  /**
   *@todo
   */
  function testShow() {

  }

	function testUploadDirIsWritable() {
		$this->assertTrue(parent::createFile('upload'));
		$this->assertTrue(parent::removeFile('upload'));
	}

	function testCacheDirIsWritable() {
		$this->assertTrue(parent::createFile('cache'));
		$this->assertTrue(parent::removeFile('cache'));
	}

	function testCompileDirIsWritable() {
		$this->assertTrue(parent::createFile('compile'));
		$this->assertTrue(parent::removeFile('compile'));
	}

	function testBackupDirIsWritable() {
		$this->assertTrue(parent::createFile('backup'));
		$this->assertTrue(parent::removeFile('backup'));
	}

	function testLogsDirIsWritable() {
		$this->assertTrue(parent::createFile('logs'));
		$this->assertTrue(parent::removeFile('logs'));
	}
}

class WebTestOfIndexController extends CandyWebTest {

	function setUp() {
		$this->oObject = new Index($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
	}

	function testShowIndex() {
		$this->assertTrue($this->get(WEBSITE_URL));
		$this->assertResponse(200);
		$this->assertText('TEST'); # This should be on every page.
	}

	/**
	 *@todo fix 404 to i18n
	 */
	function testShowNonExistingPage() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . md5(RANDOM_HASH)));
		$this->assertResponse(200);
		$this->assertText('404');
	}

	/**
	 *@todo fix 404 to i18n
	 */
	function testShowSampleAddon() {
		$this->assertTrue($this->get(WEBSITE_URL . '/sample'));
		$this->assertResponse(200);
		$this->assertText('Sample');
		$this->assertNoText('404');
	}
}
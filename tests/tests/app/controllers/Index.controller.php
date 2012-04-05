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

require_once PATH_STANDARD . '/vendor/candyCMS/core/controllers/Index.controller.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Sessions.model.php';

use \CandyCMS\Core\Controllers\Index;
use \CandyCMS\Core\Helpers\I18n;

class UnitTestOfIndexController extends CandyUnitTest {

	function setUp() {
		$this->oObject = new Index($this->aRequest, $this->aSession);
	}

	function tearDown() {
		parent::tearDown();
    $this->oObject->__destruct();
	}

	function testGetConfigFiles() {
		$this->assertTrue(file_exists(PATH_STANDARD . '/app/config/Candy.inc.php'), 'Candy.inc.php exists.');
		$this->assertTrue(file_exists(PATH_STANDARD . '/app/config/Plugins.inc.php'), 'Plugins.inc.php exists.');
		$this->assertTrue($this->oObject->getConfigFiles(array('Candy', 'Plugins')));
	}

	function testGetPlugins() {
    #null, because in testmode there are no plugins loaded
		$this->assertNull($this->oObject->getPlugins(ALLOW_PLUGINS));
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
		$this->assertTrue(parent::createFile(CACHE_DIR));
		$this->assertTrue(parent::removeFile(CACHE_DIR));
	}

	function testCompileDirIsWritable() {
		$this->assertTrue(parent::createFile(COMPILE_DIR));
		$this->assertTrue(parent::removeFile(COMPILE_DIR));
	}

	function testBackupDirIsWritable() {
		$this->assertTrue(parent::createFile('app/backup'));
		$this->assertTrue(parent::removeFile('app/backup'));
	}

	function testLogsDirIsWritable() {
		$this->assertTrue(parent::createFile('app/logs'));
		$this->assertTrue(parent::removeFile('app/logs'));
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

	function testShowNonExistingPage() {
		$this->assertTrue($this->get(WEBSITE_URL . '/' . md5(RANDOM_HASH)));
		$this->assert404();
	}

	function testShowSampleExtension() {
		$this->assertTrue($this->get(WEBSITE_URL . '/sample'));
		$this->assertResponse(200);
		$this->assertText('Sample');
		$this->assertNoText('404');
	}
}
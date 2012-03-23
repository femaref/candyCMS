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

require_once PATH_STANDARD . '/app/helpers/SmartySingleton.helper.php';

use CandyCMS\Helper\SmartySingleton as SmartySingleton;

if (!defined('WEBSITE_LANGUAGE'))
  define('WEBSITE_LANGUAGE', 'en');

if (!defined('WEBSITE_LOCALE'))
  define('WEBSITE_LOCALE', 'en_US');

class UnitTestOfSmartySingletonHelper extends CandyUnitTest {

	function setUp() {
    # Bugfix
    $_SESSION = array('lang' => null);

    $this->oObject = SmartySingleton::getInstance();
	}

	function tearDown() {
		parent::tearDown();
    $this->oObject->__destruct();
	}

  function testIsSmarty() {
    $this->assertTrue(is_a($this->oObject, "Smarty"));
  }

  function testSingleton() {
    $this->assertSame(SmartySingleton::getInstance(), $this->oObject);
  }

  function testGetPaths() {
    $aPaths = $this->oObject->getPaths();
    $aExpectedKeys = array('css', 'less', 'js', 'images', 'public', 'template', 'upload');
    $this->assertEqual(count($aPaths), count($aExpectedKeys));

    foreach ($aExpectedKeys as $sKey)
      $this->assertNotNull($aPaths[$sKey]);
  }

  function testClearCacheForController() {
    # fill the cache
    $oSmarty = SmartySingleton::getInstance();
    $oSmarty->setCaching(SmartySingleton::CACHING_LIFETIME_SAVED);
    $oSmarty->setTemplateDir(PATH_STANDARD . '/tests/tests/app/views');
    $oSmarty->fetch('helloworld.tpl', 'test|mytest|hello');

    $this->assertTrue(file_exists(PATH_STANDARD . '/' . CACHE_DIR . '/test/mytest/hello/'));

    $aPaths = $this->oObject->clearCacheForController('mytest');
    $this->assertFalse(file_exists(PATH_STANDARD . '/' . CACHE_DIR . '/test/mytest/hello/'));
  }

  function testSetRequestAndSession() {
    $this->assertNull($this->oObject->getTemplateVars('_REQUEST'));
    $this->assertNull($this->oObject->getTemplateVars('_SESSION'));
    $this->oObject->setRequestAndSession($this->aRequest, $this->aSession);
    $this->assertNotNull($this->oObject->getTemplateVars('_REQUEST'));
    $this->assertNotNull($this->oObject->getTemplateVars('_SESSION'));
  }
}
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
}
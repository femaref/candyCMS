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

require_once PATH_STANDARD . '/app/helpers/Helper.helper.php';

use \CandyCMS\Helper\Helper as Helper;

class UnitTestOfHelperHelpers extends CandyUnitTest {

	function setUp() {
	}

	function tearDown() {
		parent::tearDown();
	}

  function testSuccessMessage() {
    $this->assertTrue(Helper::successMessage('Message'));
  }

  function testErrorMessage() {
    $this->assertFalse(Helper::errorMessage('Message'));
  }

  /**
   * @todo
   */
  function testCheckEmailAddress() {
    $this->assertTrue(Helper::checkEmailAddress('admin@example.com'));
    #$this->assertFalse(Helper::checkEmailAddress('admin@example'));
    $this->assertFalse(Helper::checkEmailAddress('admin'));
  }

  function testCreateRandomChar() {
    $this->assertEqual(strlen(Helper::createRandomChar(10)), '10');
    $this->assertTrue(is_numeric(Helper::createRandomChar(3, true)));
  }

  function testCreateLinkTo() {
    $this->assertFalse(preg_match('/external/i', Helper::createLinkTo('/')));
    $this->assertPattern('/external/i', Helper::createLinkTo('/', true));
  }

  function testGetAvatar() {
    $this->assertPattern('/gravatar/i', Helper::getAvatar(100, 0));
  }

  function testGetFileSize() {
    $this->assertPattern('/KB/i', Helper::getFileSize('index.php'));
    $this->assertPattern('/Byte/i', Helper::getFileSize('not_existing_file'));
  }

  function testGetTemplateDir() {
    $this->assertPattern('/app\/views/i', Helper::getTemplateDir('layouts', 'application'));
    $this->assertPattern('/addons\/views/i', Helper::getTemplateDir('samples', 'show'));
  }

  function testGetTemplateType() {
    $this->assertPattern('/application.tpl/i', Helper::getTemplateType('app/views/layouts', 'application'));
  }

  function testGetPluginTemplateDir() {
    $this->assertPattern('/plugins\/views/i', Helper::getPluginTemplateDir('headlines', 'show'));
  }

  function testFormatInput() {
    $this->assertPattern('/&quot;/i', Helper::formatInput('"'));
    $this->assertPattern('/</i', Helper::formatInput('<', false));
  }

  function testFormatTimestamp() {
    $this->assertTrue(Helper::formatTimestamp(0) == strftime(DEFAULT_DATE_FORMAT . ', ' . DEFAULT_TIME_FORMAT, 0));
    $this->assertTrue(Helper::formatTimestamp(0, 1) == strftime(DEFAULT_DATE_FORMAT, 0));
    $this->assertTrue(Helper::formatTimestamp(0, 2) == strftime(DEFAULT_TIME_FORMAT, 0));
  }
}
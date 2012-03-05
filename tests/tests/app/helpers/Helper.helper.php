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
    $this->assertTrue(preg_match('/external/i', Helper::createLinkTo('/', true)));
  }

  function testGetAvatar() {
    $this->assertTrue(preg_match('/gravatar/i', Helper::getAvatar(100, 0)));
  }

  function testGetFileSize() {
    $this->assertTrue(preg_match('/KB/i', Helper::getFileSize('index.php')));
    $this->assertTrue(preg_match('/Byte/i', Helper::getFileSize('not_existing_file')));
  }

  function testGetTemplateDir() {
    $this->assertTrue(preg_match('/app\/views/i', Helper::getTemplateDir('layouts', 'application')));
    $this->assertTrue(preg_match('/addons\/views/i', Helper::getTemplateDir('samples', 'show')));
  }

  function testGetTemplateType() {
    $this->assertTrue(preg_match('/application.tpl/i', Helper::getTemplateType('app/views/layouts', 'application')));
  }

  function testGetPluginTemplateDir() {
    $this->assertTrue(preg_match('/plugins\/views/i', Helper::getPluginTemplateDir('headlines', 'show')));
  }

  function testFormatInput() {
    $this->assertTrue(preg_match('/&quot;/i', Helper::formatInput('"')));
    $this->assertTrue(preg_match('/</i', Helper::formatInput('<', false)));
  }

  function testFormatTimestamp() {
    $this->assertTrue(Helper::formatTimestamp(0) == strftime(DEFAULT_DATE_FORMAT . ', ' . DEFAULT_TIME_FORMAT, 0));
    $this->assertTrue(Helper::formatTimestamp(0, 1) == strftime(DEFAULT_DATE_FORMAT, 0));
    $this->assertTrue(Helper::formatTimestamp(0, 2) == strftime(DEFAULT_TIME_FORMAT, 0));
  }
}
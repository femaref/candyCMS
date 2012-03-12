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

class UnitTestOfHelperHelper extends CandyUnitTest {

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

  function testCheckEmailAddress() {
    $this->assertTrue(Helper::checkEmailAddress('admin@example.com'));
    $this->assertTrue(Helper::checkEmailAddress('admin@sub.domain.example.com'));
    $this->assertTrue(Helper::checkEmailAddress('admin-helper@example.com'));
    $this->assertTrue(Helper::checkEmailAddress('admin-helper__-@example.com'));

    $this->assertFalse(Helper::checkEmailAddress('admin...helper@example.com'));
    $this->assertFalse(Helper::checkEmailAddress('admin.@example.com'));

    $this->assertFalse(Helper::checkEmailAddress('admin@example'));
    //should fail becaus tld is too short (2-6)
    $this->assertFalse(Helper::checkEmailAddress('a@b.c'));
    //should fail becaus tld is too long (2-6)
    $this->assertFalse(Helper::checkEmailAddress('a@b.cdefghi'));
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

  function testCreateAvatarURLs() {
    $arr = array();
    $x = Helper::createAvatarURLs($arr, 0, '', false);
    $this->assertEqual($arr['avatar_32'], Helper::getAvatar(32, 0));
    $this->assertEqual($arr['avatar_64'], Helper::getAvatar(64, 0));
    $this->assertEqual($arr['avatar_100'], Helper::getAvatar(100, 0));
    $this->assertEqual($arr['avatar_popup'], Helper::getAvatar('popup', 0));
    $this->assertNotNull($arr['avatar_32']);
    $this->assertNotNull($arr['avatar_64']);
    $this->assertNotNull($arr['avatar_100']);
    $this->assertNotNull($arr['avatar_popup']);
    $this->assertSame($x, $arr);
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
    $this->assertPattern('/Headlines/i', Helper::getPluginTemplateDir('headlines', 'show'));
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

  function testFormatOutput() {
    $this->assertPattern('/<mark>/i', Helper::formatOutput('test', 'test'));
  }

  function testGetLastEntry() {
    $this->assertTrue((int) Helper::getLastEntry('logs') > 0);
  }

  function testReplaceNonAlphachars() {
    $this->assertPattern('/_/i', Helper::replaceNonAlphachars(' '));
  }

  function testRemoveSlash() {
    $this->assertEqual(Helper::removeSlash('/test'), 'test');
    $this->assertEqual(Helper::removeSlash('//test'), '/test');
  }

  function testAddSlash() {
    $this->assertEqual(Helper::addSlash('test'), '/test');
    $this->assertEqual(Helper::addSlash('/test'), '/test');
  }
}
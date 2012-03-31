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

require_once PATH_STANDARD . '/app/helpers/AdvancedException.helper.php';
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

    # should fail becaus tld is too short (2-6)
    $this->assertFalse(Helper::checkEmailAddress('a@b.c'));

    # should fail becaus tld is too long (2-6)
    $this->assertFalse(Helper::checkEmailAddress('a@b.cdefghi'));
    $this->assertFalse(Helper::checkEmailAddress('a  b@domain.com'));
    $this->assertFalse(Helper::checkEmailAddress('admindomain.com'));
    $this->assertFalse(Helper::checkEmailAddress('admin'));
  }

  private function _containsNumber($sString) {
    for ($iI = 0; $iI < strlen($sString); $iI++)
      if (is_numeric($sString[$iI]))
        return true;
  }

  function testCreateRandomChar() {
    $this->assertEqual(strlen(Helper::createRandomChar(10)), '10');
    $this->assertEqual(strlen(Helper::createRandomChar(3)), '3');
    $this->assertTrue($this->_containsNumber(Helper::createRandomChar(10, true)));

    # speakable passwords shall not start with numbers
    $aPassword = Helper::createRandomChar(10, true);
    $this->assertFalse(is_numeric($aPassword[0]));
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
    $this->assertFalse(Helper::getTemplateDir(time(), time()));
  }

  function testGetTemplateType() {
    $this->assertPattern('/application.tpl/i', Helper::getTemplateType('app/views/layouts', 'application'));
  }

  function testGetPluginTemplateDir() {
    $this->assertPattern('/Headlines/i', Helper::getPluginTemplateDir('headlines', 'show'));
    $this->assertFalse(Helper::getTemplateDir(time(), time()));
  }

  function testFormatInput() {
    $this->assertPattern('/&quot;/i', Helper::formatInput('"'));
    $this->assertPattern('/</i', Helper::formatInput('<', false));
    $this->assertPattern('/&lt;/i', Helper::formatInput('<', true));
  }

  function testFormatTimestamp() {
    $this->assertEqual(Helper::formatTimestamp(1), strftime(DEFAULT_DATE_FORMAT . ', ' . DEFAULT_TIME_FORMAT, 1));
    $this->assertEqual(Helper::formatTimestamp(1, 1), strftime(DEFAULT_DATE_FORMAT, 1));
    $this->assertEqual(Helper::formatTimestamp(1, 2), strftime(DEFAULT_TIME_FORMAT, 1));
  }

  function testFormatOutput() {
    $this->assertPattern('/<mark>/i', Helper::formatOutput('test', 'test'));
    $this->assertNoPattern('/<mark>/i', Helper::formatOutput('test'));
  }

  function testGetLastEntry() {
    $this->assertTrue((int) Helper::getLastEntry('logs') > 0);
  }

  function testReplaceNonAlphachars() {
    $this->assertEqual('AeUeOeaeueoess', Helper::replaceNonAlphachars('ÄÜÖäüöß'));
    $this->assertEqual('_', Helper::replaceNonAlphachars(' '));
    $this->assertEqual('.', Helper::replaceNonAlphachars('.'));
    $this->assertEqual('12345', Helper::replaceNonAlphachars('12345'));
  }

  function testRemoveSlash() {
    $this->assertEqual(Helper::removeSlash('/test'), 'test');
    $this->assertEqual(Helper::removeSlash('//test'), '/test');
  }

  function testAddSlash() {
    $this->assertEqual(Helper::addSlash('test'), '/test');
    $this->assertEqual(Helper::addSlash('/test'), '/test');
  }

  function testPluralize() {
    $this->assertEqual(Helper::pluralize('kiss'), 'kisses');
    $this->assertEqual(Helper::pluralize('phase'), 'phases');
    $this->assertEqual(Helper::pluralize('dish'), 'dishes');
    $this->assertEqual(Helper::pluralize('judge'), 'judges');
    $this->assertEqual(Helper::pluralize('baby'), 'babies');
  }

  function testSingleize() {
    $this->assertEqual(Helper::singleize('babies'), 'baby');
    $this->assertEqual(Helper::singleize('kiss'), 'kiss');
    $this->assertEqual(Helper::singleize('searches'), 'search');
  }
}

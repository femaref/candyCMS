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

require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Main.model.php';
require_once PATH_STANDARD . '/vendor/candyCMS/core/helpers/Helper.helper.php';

use \CandyCMS\Core\Models\Main;
use \CandyCMS\Core\Helpers\Helper;

class MyMain extends Main {
  // since the Main class is abstract

  // wrapper for _formatDates
  public function formatDates(&$aData) {
    return $this->_formatDates($aData);
  }

  // wrapper for _formatForUpdate
  public function formatForUpdate($aRow) {
    return $this->_formatForUpdate($aRow);
  }

  // wrapper for _formatForOutput
  public function formatForOutput(&$aData, $aInts = array('id'), $aBools = null, $sController = '') {
    return $this->_formatForOutput($aData, $aInts, $aBools, $sController);
  }

  // wrapper for _formatForUserOutput
  public function formatForUserOutput(&$aData) {
    return $this->_formatForUserOutput($aData);
  }
}

class UnitTestOfBlogModel extends CandyUnitTest {

  function setUp() {
    $this->aRequest = array(
        'controller'  => 'main');

    $this->oObject = new MyMain($this->aRequest, $this->aSession);
  }

	function tearDown() {
		parent::tearDown();
	}

  function testConnectToDatabase() {
    $oDB = MyMain::connectToDatabase();
    $this->assertNotNull($oDB);
    $this->assertSame($oDB, MyMain::connectToDatabase());
  }

  function testDisconnectToDatabase() {
    $this->assertNull(MyMain::disconnectFromDatabase());
  }

  function testFormatForUpdate() {
    // TODO
  }

  function testFormatForOutput() {
    $aData = array(
        'user_email' => 'some@email.com',
        'user_id' => 1,
        'user_name' => 'Test',
        'user_surname' => 'User',
        'date' => 1,
        'id' => 42,
        'title' => 'Funky Title With Spaces'
    );

    $aXData = $this->oObject->formatForOutput($aData, null, null, 'maincontroller');
    $this->assertSame($aXData, $aData, 'FormatForOutput should return reference only');

    $this->assertTrue(isset($aData['author']));
    $this->assertEqual('Test User', $aData['author']['full_name']);
    $this->assertEqual(WEBSITE_URL . '/maincontroller/42', $aData['url_clean']);
    $this->assertEqual('/update', substr($aData['url_update'], -7));
    $this->assertEqual('/destroy', substr($aData['url_destroy'], -8));
    $this->assertTrue(isset($aData['datetime']));
  }

  function testFormatDates() {
    $aData = array( 'no_date' => 'hello' );
    $this->oObject->formatDates($aData);
    $this->assertFalse(isset($aData['date']));
    $this->assertFalse(isset($aData['time']));

    $iTimeStamp = time();
    $aData = array( 'date' => $iTimeStamp );
    $aXData = $this->oObject->formatDates($aData);
    $this->assertSame($aXData, $aData, 'formatDates should return reference only');

    $this->assertEqual($iTimeStamp, $aData['date_raw']);
    $this->assertEqual(date('Y-m-d', $iTimeStamp), $aData['date_w3c']);
    $this->assertEqual(Helper::formatTimestamp($iTimeStamp, 2), $aData['time']);
    $this->assertEqual(Helper::formatTimestamp($iTimeStamp, 1), $aData['date']);
    $this->assertEqual(Helper::formatTimestamp($iTimeStamp), $aData['datetime']);
    $this->assertEqual(date('D, d M Y H:i:s O', $iTimeStamp), $aData['datetime_rss']);
    $this->assertEqual(date('Y-m-d\TH:i:sP', $iTimeStamp), $aData['datetime_w3c']);
  }

  function testFormatForUserOutput() {
    $aUserData = array(
          'email'         => 'some@email.com',
          'id'            => 1,
          'use_gravatar'  => true,
          'name'          => 'Test',
          'surname'       => 'User',
          'facebook_id'   => '',
          'ip'            => '127.0.0.1',
      );

    $aXData = $this->oObject->formatForUserOutput($aUserData);
    $this->assertSame($aXData, $aUserData, 'FormatForUserOutput should return reference only');

    $this->assertEqual('Test User', $aUserData['full_name']);
    $this->assertEqual('Test+User', $aUserData['encoded_full_name']);
    $this->assertEqual(WEBSITE_URL . '/users/1', $aUserData['url_clean']);
    $this->assertEqual('/update', substr($aUserData['url_update'], -7));
    $this->assertEqual('/destroy', substr($aUserData['url_destroy'], -8));
    $this->assertFalse(isset($aUserData['date']));


    $aUserData = array(
        'email' => 'some@email.com',
        'id' => 1,
        'use_gravatar' => true,
        'name' => 'Test',
        'surname' => 'User',
        'facebook_id' => '',
        'ip' => '127.0.0.1',
        'date' => 1
    );

    $aUpdateData = $this->oObject->formatForUserOutput($aUserData);
    $this->assertTrue(isset($aUserData['date']));
    $this->assertTrue(isset($aUserData['datetime']));
    $this->assertTrue(isset($aUserData['time']));
    $this->assertTrue(isset($aUserData['datetime_rss']));
  }

  function testGetLastInsertId() {
    // TODO
  }

  function testAutoload() {
    // TODO
  }

  function testGetTypeaheadData() {
    // TODO
  }

  function testDestroy() {
    // TODO
  }
}
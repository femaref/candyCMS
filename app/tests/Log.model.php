<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

require_once('lib/simpletest/autorun.php');
require_once('app/models/Log.model.php');

use \CandyCMS\Model\Log as Log;

class TestOfLogModel extends UnitTestCase {

  public $oLog;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array('section' => 'log');

    $aSession['userdata'] = array(
        'email' => '',
        'facebook_id' => '',
        'id' => 0,
        'name' => '',
        'surname' => '',
        'password' => '',
        'user_right' => 0,
        'full_name' => ''
    );

    $aFile = array();
    $aCookie  = array();

    $this->oLog = new Log($aRequest, $aSession, $aFile, $aCookie);
  }

  function testCreate() {
    $this->assertTrue(Log::insert('test', 'create', 1, 0, time(), time()));

    $this->iLastInsertId = (int) Log::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'Log #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oLog->getData(), 'array');
  }

  function testDestroy() {
    $this->assertTrue($this->oLog->destroy($this->iLastInsertId), 'Log #' .$this->iLastInsertId. ' destroyed.');
  }
}
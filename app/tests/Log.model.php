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

    $aRequest = array();
    $aSession = array();
    $aCookie  = array();

    $this->oLog = new Log($aRequest, $aSession, $aCookie, '');
  }

  function testCreate() {
    $this->assertTrue(Log::insert('test', 'create', 1, 0, time(), time()));

    $this->iLastInsertId = Log::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'string', 'Log #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oLog->getData(), 'array');
  }

  function testDestroy() {
    $this->assertTrue($this->oLog->destroy($this->iLastInsertId), 'Log #' .$this->iLastInsertId. ' destroyed.');
  }
}
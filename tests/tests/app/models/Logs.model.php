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

require_once PATH_STANDARD . '/app/models/Logs.model.php';

use \CandyCMS\Model\Logs as Logs;

class UnitTestOfLogModel extends CandyUnitTest {

  function setUp() {

    $this->aRequest = array('section' => 'logs');

    $this->oObject = new Logs($this->aRequest, $this->aSession);
  }

  function testCreate() {
    $this->assertTrue(Logs::insert('test', 'create', 1, 0, time(), time()));

    $this->iLastInsertId = (int) Logs::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer');
  }

  function testGetData() {
    $this->assertIsA($this->oObject->getData(), 'array');
  }

  function testDestroy() {
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId));
  }
}
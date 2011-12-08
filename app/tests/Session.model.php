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
require_once('app/models/Session.model.php');

use \CandyCMS\Model\Session as Session;

class TestOfSessionModel extends UnitTestCase {

  public $oSession;
  public $iLastInsertId;

  function testConstructor() {

    $aRequest = array('email' => 'email@example.com',
                      'password' => 'Password');
    $aSession = array();
    $aCookie  = array();

    $this->oSession = new Session($aRequest, $aSession, $aCookie, '');
  }

  function testCreate() {
    # Can not login with that data
    $this->assertFalse($this->oSession->create(), 'Session created.');
  }

  function testGetData() {
    $this->assertIsA($this->oSession->getData(), 'array');
  }

  function testUpdate() {
    # User doesn't exist, but query run through
    $this->assertTrue($this->oSession->update(0), 'Session updated.');
  }

  function testDestroy() {
    # User doesn't exist, but query run through
    $this->assertTrue($this->oSession->destroy($this->iLastInsertId), 'Session #' .$this->iLastInsertId. ' destroyed.');
  }
}
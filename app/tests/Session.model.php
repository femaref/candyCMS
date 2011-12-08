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

  function testCreateResendActions() {
    # Can not login with that data
    # False, because user doesn't exist
    #$this->assertFalse($this->oSession->createResendActions('new_password'), 'Resend actions.');
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
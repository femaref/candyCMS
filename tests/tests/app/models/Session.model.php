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

    $aRequest = array(
        'email' => 'email@example.com',
        'password' => 'Password',
        'section' => 'search');

    $aSession['user'] = array(
        'email' => '',
        'facebook_id' => '',
        'id' => 0,
        'name' => '',
        'surname' => '',
        'password' => '',
        'role' => 0,
        'full_name' => ''
    );

    $aFile    = array();
    $aCookie  = array();

    $this->oSession = new Session($aRequest, $aSession, $aFile, $aCookie);
  }

  # Create a new session.
  function testCreate() {
    $this->assertFalse($this->oSession->create(), 'Session created.');
  }

  # Session will not be found, so we expect no return.
  function testGetUserBySession() {
    $this->assertFalse(Session::getUserBySession());
  }

  # We try to resend the password. Email address will not be found, so we expect a false.
  function testResendPassword() {
    $this->assertFalse($this->oSession->resendPassword());
  }

  # We try to resend the verification. Email address will not be found, so we expect a false.
  function testResendVerification() {
    $this->assertFalse($this->oSession->resendVerification());
  }

  # User doesn't exist, but query run through.
  function testDestroy() {
    $this->assertTrue($this->oSession->destroy(), 'Session destroyed.');
  }
}
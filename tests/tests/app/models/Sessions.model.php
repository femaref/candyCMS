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

require_once PATH_STANDARD . '/app/models/Sessions.model.php';

use \CandyCMS\Model\Sessions as Sessions;

class TestOfSessionsModel extends UnitTestCase {

  function testConstructor() {

    $this->aRequest = array(
        'email' => 'email@example.com',
        'password' => 'Password',
        'section' => 'search');

    $this->oObject = new Sessions($this->aRequest, $this->aSession);
  }

  # Create a new session.
  function testCreate() {
    $this->assertFalse($this->oObject->create());
  }

  # Session will not be found, so we expect no return.
  function testGetUserBySession() {
    $this->assertFalse(Sessions::getUserBySession());
  }

  # We try to resend the password. Email address will not be found, so we expect a false.
  function testResendPassword() {
    $this->assertFalse($this->oObject->resendPassword());
  }

  # We try to resend the verification. Email address will not be found, so we expect a false.
  function testResendVerification() {
    $this->assertFalse($this->oObject->resendVerification());
  }

  # User doesn't exist, but query run through.
  function testDestroy() {
    $this->assertTrue($this->oObject->destroy());
  }
}
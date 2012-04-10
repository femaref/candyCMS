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

require_once PATH_STANDARD . '/vendor/candyCMS/core/models/Sessions.model.php';

use \CandyCMS\Core\Models\Sessions;

class TestOfSessionsModel extends UnitTestCase {

  function setUp() {
    parent::setUp();

    $this->aRequest = array(
        'email'       => 'unknownemailaddress@example.com',
        'password'    => 'test',
        'controller'  => 'sessions');

    $this->oObject = new Sessions($this->aRequest, $this->aSession);
  }

  # Create a new session.
  function testCreate() {
    # fails due to not verified...
    $this->aRequest['email'] = 'unverified@example.com';
    $this->assertFalse($this->oObject->create());

    # but verified users works
    $this->aRequest['email'] = 'moderator@example.com';
    $this->assertTrue($this->oObject->create());
    $this->oObject->destroy(session_id());
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
    $this->assertTrue($this->oObject->destroy(session_id()));
  }
}
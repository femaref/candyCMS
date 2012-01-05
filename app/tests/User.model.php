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
require_once('app/models/User.model.php');

use \CandyCMS\Model\User as User;
use \CandyCMS\Model\Session as Session;

class TestOfUserModel extends TestOfSessionModel {

  public $oUser;
  public $iLastInsertId;
  public $aRequest;
  public $aSession;

  function testConstructor() {

    $aRequest = array(
        'email' => 'email@example.com',
        'password' => 'Password',
        'name' => 'Name',
        'content' => 'Content',
        'surname' => 'Surname',
        'receive_newsltter' => 0,
        'role' => 0,
        'use_gravatar' => 0,
        'section' => 'user');

    $aSession['userdata'] = array(
        'email' => '',
        'facebook_id' => '',
        'id' => 0,
        'name' => '',
        'surname' => '',
        'password' => '',
        'role' => 0,
        'full_name' => ''
    );

    $aFile = array();
    $aCookie = array();

    $this->aRequest = $aRequest;
    $this->aSession = $aSession;

    $this->oUser = new User($aRequest, $aSession, $aFile, $aCookie);
  }

  function testCreate() {
    $this->assertTrue($this->oUser->create('000000000000'));

    $this->iLastInsertId = (int) User::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'User #' . $this->iLastInsertId . ' created.');
    $this->aRequest['id'] = $this->iLastInsertId;
  }

  function testGetData() {
    $this->assertIsA($this->oUser->getData(0), 'array');
    $this->assertIsA($this->oUser->getData(), 'array');
  }

  function testGetExistingUser() {
    $this->assertTrue($this->oUser->getExistingUser($this->aRequest['email']));
    $this->assertFalse($this->oUser->getExistingUser('adsfadsfsda@fghfghfgfg.com'));
  }

  function testGetUserNameAndEmail() {
    $this->assertIsA($this->oUser->getUserNamesAndEmail($this->iLastInsertId), 'array');
  }

  # Get the verification data.
  function testGetVerificationData() {
    $this->assertIsA($this->oUser->getVerificationData($this->aRequest['email']), 'array');
  }

  # Set to default password.
  function testSetPassword() {
    $this->assertTrue($this->oUser->setPassword($this->aRequest['email'], $this->aRequest['password'], true), 'array');
  }

  # Fetch the login data.
  function testGetLoginData() {
    $this->assertIsA($this->oUser->getLoginData(), 'array');
  }

  # Get user token.
  function testGetToken() {
    $this->assertIsA($this->oUser->getToken(), 'string');
  }

  /*******************************************************
   * Session verification test start
   ******************************************************/

  # Try to login, but there's still the verification code.
  function testSessionCreate1() {
    $this->oSession = new Session($this->aRequest, $this->aSession, array(), '');
    $this->assertFalse($this->oSession->create(), 'Session created.');
  }

  # Resend verification data. Works, because it's not empty.
  function testResendVerification1() {
    $this->oSession = new Session($this->aRequest, $this->aSession, array(), '');
    $this->assertIsA($this->oSession->resendVerification(), 'array');
  }

  /*******************************************************
   * Session verification test end
   ******************************************************/

  # Try to verify the verification_code.
  function testVerifyEmail() {
    $this->assertFalse($this->oUser->verifyEmail('000100010001'));
    $this->assertTrue($this->oUser->verifyEmail('000000000000'));
  }

  # Return the user data from activation.
  function testGetActivationData() {
    $this->assertIsA($this->oUser->getActivationData(), 'array');
  }

  # Update user.
  function testUpdate() {
    $this->assertTrue($this->oUser->update($this->iLastInsertId), 'User #' . $this->iLastInsertId . ' updated.');
  }

  /*******************************************************
   * Session verification test start
   ******************************************************/

  # Create a session with existing user data.
  function testSessionCreate2() {
    $this->oSession = new Session($this->aRequest, $this->aSession, array(), '');
    $this->assertTrue($this->oSession->create($this->aRequest));
  }

  # We try to resend the password.
  function testResendPassword() {
    $this->oSession = new Session($this->aRequest, $this->aSession, array(), '');
    $this->assertTrue($this->oSession->resendPassword());
  }

  # We try to resend the verification. Verification code is already empty.
  function testResendVerification2() {
    $this->oSession = new Session($this->aRequest, $this->aSession, array(), '');
    $this->assertFalse($this->oSession->resendVerification());
  }

  # Destroy active session.
  function testSessionDestory() {
    $this->assertTrue($this->oSession->destroy(), 'Session destroyed.');
  }

  /*******************************************************
   * Session verification test end
   ******************************************************/

  # Destory our built user.
  function testDestroy() {
    $this->assertTrue($this->oUser->destroy($this->iLastInsertId), 'User #' . $this->iLastInsertId . ' destroyed.');
  }
}
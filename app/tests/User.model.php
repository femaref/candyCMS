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

    $this->oUser = new User($aRequest, $aSession, $aFile, $aCookie);
  }

  function testCreate() {
    $this->assertTrue($this->oUser->create('000000000000'));

    $this->iLastInsertId = (int) User::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'User #' . $this->iLastInsertId . ' created.');
  }

  function testGetData() {
    $this->assertIsA($this->oUser->getData(0), 'array');
    $this->assertIsA($this->oUser->getData(), 'array');
  }
  # Private method
  #function testGetPassword() {
  #  $this->assertIsA($this->oUser->_getPassword($this->iLastInsertId), 'string');
  #}

  function testGetExistingUser() {
    # Email exists
    $this->assertTrue($this->oUser->getExistingUser('email@example.com'));

    # Email doesn't exist
    $this->assertFalse($this->oUser->getExistingUser('adsfadsfsda@fghfghfgfg.com'));
  }

  function testGetUserNameAndEmail() {
    $this->assertIsA($this->oUser->getUserNamesAndEmail($this->iLastInsertId), 'array');
  }

  function testVerificationData() {
    $this->assertIsA($this->oUser->getVerificationData(), 'array');
  }

  function testVerifyEmail() {
    # Code doesn't exist
    $this->assertFalse($this->oUser->verifyEmail('000100010001'));

    # Code exists
    $this->assertTrue($this->oUser->verifyEmail('000000000000'));
  }

  function testUpdate() {
    $this->assertTrue($this->oUser->update($this->iLastInsertId), 'User #' . $this->iLastInsertId . ' updated.');
  }

  /**
   * Start of session tests
   */
  function testSessionCreate() {
    $aRequest = array(
        'email' => 'email@example.com',
        'password' => 'Password');

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

    $aCookie = array();

    $this->oSession = new Session($aRequest, $aSession, $aCookie, '');
    $this->assertTrue($this->oSession->create(), 'Session created.');
  }

  function testCreateResendActionsPW() {
    $aRequest = array(
        'email' => 'email@example.com',
        'action' => 'resendpassword');

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

    $aCookie = array();

    $this->oSession = new Session($aRequest, $aSession, $aCookie, '');
    $this->assertTrue($this->oSession->createResendActions(), 'Resend password.');
  }

  function testCreateResendActionsVC() {
    $aRequest = array(
        'email' => 'email@example.com',
        'action' => 'verification');

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

    $aCookie = array();

    $this->oSession = new Session($aRequest, $aSession, $aCookie, '');
    $this->assertFalse($this->oSession->createResendActions(), 'Resend verification.');
  }

  /**
   * End of session tests
   */
  function testDestroy() {
    $this->assertTrue($this->oUser->destroy($this->iLastInsertId), 'User #' . $this->iLastInsertId . ' destroyed.');
  }
  /* Now the same stuff but with login */
}
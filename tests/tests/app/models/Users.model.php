<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */
require_once PATH_STANDARD . '/app/models/Users.model.php';
require_once PATH_STANDARD . '/app/models/Sessions.model.php';
# if something fails, the user.model will do output with the AdvancedExeption Helper,
require_once PATH_STANDARD . '/app/helpers/AdvancedException.helper.php';
require_once PATH_STANDARD . '/app/helpers/SmartySingleton.helper.php';

use \CandyCMS\Model\Users as Users;
use \CandyCMS\Model\Sessions as Sessions;

class UnitTestOfUserModel extends CandyUnitTest {

  public $oUser;
  public $iLastInsertId;
  public $aRequest;
  public $aSession;
  public $sToken;

  function setUp() {

    $this->aRequest = array(
        'email' => 'funkyemail@example.com',
        'password' => 'Password',
        'name' => 'Name',
        'content' => 'Content',
        'surname' => 'Surname',
        'receive_newsltter' => 0,
        'role' => 0,
        'use_gravatar' => 0,
        'section' => 'user');

    $this->aSession['user'] = array(
        'email' => '',
        'facebook_id' => '',
        'id' => 0,
        'name' => '',
        'surname' => '',
        'password' => '',
        'role' => 0,
        'full_name' => ''
    );
    $this->oUser = new Users($this->aRequest, $this->aSession);
  }

  function testFullFeaturedUserCreationVerificationAndDeletion() {
    $this->assertTrue($this->oUser->create('000000000000'));

    $this->iLastInsertId = (int) Users::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'User #' . $this->iLastInsertId . ' created.');
    $this->aRequest['id'] = $this->iLastInsertId;

    # get test Data
    $this->assertIsA($this->oUser->getData(0), 'array');
    $this->assertIsA($this->oUser->getData(), 'array');

    # get existing User
    $this->assertTrue($this->oUser->getExistingUser($this->aRequest['email']));
    $this->assertFalse($this->oUser->getExistingUser('adsfadsfsda@fghfghfgfg.com'));

    # Get the Username and Email
    $this->assertIsA($this->oUser->getUserNamesAndEmail($this->iLastInsertId), 'array');

    # Get the verification data.
    $this->assertIsA($this->oUser->getVerificationData($this->aRequest['email']), 'array');

    # Set to default password.
    $this->assertTrue($this->oUser->setPassword($this->aRequest['email'], $this->aRequest['password'], true), 'array');

    # Fetch the login data.
    $this->assertIsA($this->oUser->getLoginData(), 'array');

    # Get user token.
    $this->sToken = $this->oUser->getToken(false);
    $this->assertIsA($this->sToken, 'string');

    $this->assertIsA($this->oUser->getUserByToken($this->sToken), 'array');

    /** *****************************************************
     * Session verification test start
     * **************************************************** */

    # Try to login, but there's still the verification code.
    $this->oSession = new Sessions($this->aRequest, $this->aSession);
    $this->assertFalse($this->oSession->create(), 'Session created.');

    # Resend verification data. Works, because it's not empty.
    $this->oSession = new Sessions($this->aRequest, $this->aSession);
    $this->assertIsA($this->oSession->resendVerification(), 'array');

    /** *****************************************************
     * Session verification test end
     * **************************************************** */

    # Try to verify the verification_code.
    $this->assertFalse($this->oUser->verifyEmail('000100010001'));
    $this->assertTrue($this->oUser->verifyEmail('000000000000'));

    # Return the user data from activation.
    $this->assertIsA($this->oUser->getActivationData(), 'array');

    # Update user.
    $this->assertTrue($this->oUser->update($this->iLastInsertId), 'User #' . $this->iLastInsertId . ' updated.');

    /** *****************************************************
     * Session verification test start
     * **************************************************** */

    # Create a session with existing user data.
    $this->oSession = new Sessions($this->aRequest, $this->aSession);
    $this->assertTrue($this->oSession->create($this->aRequest));

    # We try to resend the password.
    $this->oSession = new Sessions($this->aRequest, $this->aSession);
    $this->assertTrue($this->oSession->resendPassword());

    # We try to resend the verification. Verification code is already empty.
    $this->oSession = new Sessions($this->aRequest, $this->aSession);
    $this->assertFalse($this->oSession->resendVerification());

    /*     * *****************************************************
     * Session verification test end
     * **************************************************** */

    # Destory our built user.
    $this->assertTrue($this->oUser->destroy($this->iLastInsertId), 'User #' . $this->iLastInsertId . ' destroyed.');
  }

}
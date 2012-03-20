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

  public $sToken;

  function setUp() {

    $this->aRequest = array(
        'email' => 'email@example.com',
        'password' => 'Password',
        'name' => 'Name',
        'content' => 'Content',
        'surname' => 'Surname',
        'receive_newsltter' => 0,
        'role' => 0,
        'use_gravatar' => 0,
        'section' => 'user');

    $this->oObject = new Users($this->aRequest, $this->aSession);
  }

  function testFullFeaturedUserCreationVerificationAndDeletion() {
    $this->assertTrue($this->oObject->create('000000000000'));

    $this->iLastInsertId = (int) Users::getLastInsertId();
    $this->assertIsA($this->iLastInsertId, 'integer', 'User #' . $this->iLastInsertId . ' created.');
    $this->aRequest['id'] = $this->iLastInsertId;

    # get test Data
    $this->assertIsA($this->oObject->getData(0), 'array');
    $this->assertIsA($this->oObject->getData(), 'array');

    # get existing User
    $this->assertTrue($this->oObject->getExistingUser($this->aRequest['email']));
    $this->assertFalse($this->oObject->getExistingUser('adsfadsfsda@fghfghfgfg.com'));

    # Get the Username and Email
    $this->assertIsA($this->oObject->getUserNamesAndEmail($this->iLastInsertId), 'array');

    # Get the verification data.
    $this->assertIsA($this->oObject->getVerificationData($this->aRequest['email']), 'array');

    # Set to default password.
    $this->assertTrue($this->oObject->setPassword($this->aRequest['email'], $this->aRequest['password'], true), 'array');

    # Fetch the login data.
    $this->assertIsA($this->oObject->getLoginData(), 'array');

    # Get user token.
    $this->sToken = $this->oObject->getToken(false);
    $this->assertIsA($this->sToken, 'string');

    $this->assertIsA($this->oObject->getUserByToken($this->sToken), 'array');

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
    $this->assertFalse($this->oObject->verifyEmail('000100010001'));
    $this->assertTrue($this->oObject->verifyEmail('000000000000'));

    # Return the user data from activation.
    $this->assertIsA($this->oObject->getActivationData(), 'array');

    # Update user.
    $this->assertTrue($this->oObject->update($this->iLastInsertId), 'User #' . $this->iLastInsertId . ' updated.');

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
    $this->assertTrue($this->oObject->destroy($this->iLastInsertId), 'User #' . $this->iLastInsertId . ' destroyed.');
  }

}
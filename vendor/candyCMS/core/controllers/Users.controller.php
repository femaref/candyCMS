<?php

/**
 * CRUD action of users.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Core\Controllers;

use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\I18n;
use CandyCMS\Core\Helpers\Upload;
use CandyCMS\Plugins\Recaptcha;

class Users extends Main {

  /**
   * Route to right action.
   *
   * @access public
   * @return string HTML
   *
   */
  public function show() {
    if (!isset($this->_aRequest['action']))
       $this->_aRequest['action'] = 'show';

    switch ($this->_aRequest['action']) {

      case 'avatar':

        $this->setTitle(I18n::get('users.title.avatar'));
        return $this->updateAvatar();

        break;

      case 'password':

        $this->setTitle(I18n::get('users.title.password'));
        return $this->updatePassword();

        break;

      case 'token':

        $this->setTitle(I18n::get('global.api_token'));
        return $this->getToken();

        break;

      case 'verification':

        $this->setTitle(I18n::get('global.email.verification'));
        return $this->verifyEmail();

        break;

      default:
      case 'show':

        $this->oSmarty->setCaching(\CandyCMS\Core\Helpers\SmartySingleton::CACHING_LIFETIME_SAVED);
        return $this->_show();

        break;
    }
  }

  /**
   * Show user or user overview.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    if ($this->_iId) {
      $sTemplateDir   = Helper::getTemplateDir($this->_aRequest['controller'], 'show');
      $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

      if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
        $aData = $this->_oModel->getData($this->_iId);

       if (!isset($aData) || !$aData[1]['id'])
          return Helper::redirectTo('/errors/404');

        $this->oSmarty->assign('user', $aData);

        $this->setTitle($aData[1]['full_name']);
        $this->setDescription(I18n::get('users.description.show', $aData[1]['full_name']));
      }

      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
    else {
      if ($this->_aSession['user']['role'] < 3)
        return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

      else {
        $sTemplateDir   = Helper::getTemplateDir($this->_aRequest['controller'], 'overview');
        $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'overview');

        $this->oSmarty->assign('user', $this->_oModel->getData());

        $this->setTitle(I18n::get('users.title.overview'));
        $this->oSmarty->setTemplateDir($sTemplateDir);
        return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
      }
    }
  }

  /**
   * Build form template to create or update a user.
   *
   * @access protected
   * @param boolean $bUseRequest whether the Displayed Data should be overwritten by Query Result
   * @return string HTML content
   *
   */
  protected function _showFormTemplate($bUseRequest = false) {
    $sTemplateDir   = Helper::getTemplateDir($this->_aRequest['controller'], '_form');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, '_form');

    # Set user id of person to update
    $iId =  $this->_iId !== $this->_aSession['user']['id'] && $this->_aSession['user']['role'] == 4 ?
            $this->_iId :
            $this->_aSession['user']['id'];

    # Fetch data from database
    $aData = $this->_oModel->getData($iId, false, true);

    # Add the gravatar_urls, so the user can preview those
    Helper::createAvatarURLs($aData, $aData['id'], $aData['email'], true);

    # Override if we want to use request
    if ($bUseRequest === true) {
      foreach ($aData as $sColumn => $sData)
        $aData[$sColumn] = isset($this->_aRequest[$sColumn]) ? $this->_aRequest[$sColumn] : $sData;
    }

    foreach ($aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->assign('uid', $iId);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Upload user profile image.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function updateAvatar() {
    return isset($this->_aRequest['create_avatar']) ?
            $this->_updateAvatar() :
            $this->_showFormTemplate();
  }

  /**
   * Upload user profile image.
   *
   * Check for required Fields, show Form if Fields are missing,
   * otherwise upload new Avatar, unset Gravatar on success and redirect to user Profile
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function _updateAvatar() {
    $this->_setError('terms', I18n::get('error.file.upload'));
    $this->_setError('image');

    require PATH_STANDARD . '/vendor/candyCMS/core/helpers/Upload.helper.php';
    $oUpload = new Upload($this->_aRequest, $this->_aSession, $this->_aFile);

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($oUpload->uploadAvatarFile(false) === true) {
      $this->_oModel->updateGravatar($this->_iId);

      return Helper::successMessage(I18n::get('success.upload'), '/' .
              $this->_aRequest['controller'] . '/' . $this->_iId);
    }

    else
      return Helper::errorMessage(I18n::get('error.file.upload'), '/' .
              $this->_aRequest['controller'] . '/' . $this->_iId . '/update');
  }

  /**
   * Update a users password.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function updatePassword() {
    return isset($this->_aRequest['update_password']) ?
            $this->_updatePassword() :
            $this->_showFormTemplate();
  }

  /**
   * Update a users password.
   *
   * Check for required Fields, show Form if Fields are missing or wrong,
   * otherwise change the password and redirect to user Profile
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _updatePassword() {
    # Check if old password is set
    $this->_setError('password_old', I18n::get('error.user.update.password.old.empty'));

    # Check if new password fields aren't empty
    $this->_setError('password_new', I18n::get('error.user.update.password.new.empty'));
    $this->_setError('password_new2', I18n::get('error.user.update.password.new.empty'));

    # Check if old password is correct, emptyness is checked by _setError
    if (md5(RANDOM_HASH . $this->_aRequest['password_old']) !== $this->_aSession['user']['password'])
      $this->_aError['password_old'] = I18n::get('error.user.update.password.old.wrong');

    # Check if new password fields match
    if ($this->_aRequest['password_new'] !== $this->_aRequest['password_new2'])
      $this->_aError['password_new'] = I18n::get('error.user.update.password.new.match');

    $sRedirectURL = '/' . $this->_aRequest['controller'] . '/';

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->updatePassword((int) $this->_iId) === true) {
      $this->_iId = isset($this->_aRequest['id']) ?
              (int) $this->_aRequest['id'] :
              $this->_aSession['user']['id'];

      Logs::insert(  $this->_aRequest['controller'],
                    $this->_aRequest['action'],
                    (int) $this->_iId,
                    $this->_aSession['user']['id']);

      return Helper::successMessage(I18n::get('success.update'), $sRedirectURL . $this->_iId);
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), $sRedirectURL . $this->_iId);
  }

  /**
   * Create user or show form template.
   *
   * This method must override the parent one because of another showTemplate method.
   *
   * @access public
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  public function create() {
    # Logged in users should not have a recaptcha field since we can assume that these are real humans.
    $bShowCaptcha = class_exists('\CandyCMS\Plugins\Recaptcha') ?
            $this->_aSession['user']['role'] == 0 && SHOW_CAPTCHA :
            false;

    if($this->_aSession['user']['role'] > 0 && $this->_aSession['user']['role'] < 4)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

    else
      return isset($this->_aRequest['create_users']) ?
              $this->_create($bShowCaptcha) :
              $this->_showCreateUserTemplate($bShowCaptcha);
  }

  /**
   * Create a user.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, activate the model, insert them into the database, send mail and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create($bShowCaptcha) {
    $this->_setError('name')->_setError('surname')->_setError('email')->_setError('password');

    if ($this->_oModel->getExistingUser($this->_aRequest['email']))
      $this->_aError['email'] = I18n::get('error.user.create.email');

    if ($this->_aRequest['password'] !== $this->_aRequest['password2'])
      $this->_aError['password'] = I18n::get('error.passwords');

    # Admin does not need to confirm disclaimer
    if ($this->_aSession['user']['role'] < 4 && !isset($this->_aRequest['disclaimer']))
      $this->_aError['disclaimer'] = I18n::get('error.form.missing.terms');

    if ($bShowCaptcha === true && Recaptcha::getInstance()->checkCaptcha($this->_aRequest) === false)
        $this->_aError['captcha'] = I18n::get('error.captcha.incorrect');

    # Generate verification code for users (double-opt-in) when not created by admin.
    $iVerificationCode = $this->_aSession['user']['role'] < 4 ? Helper::createRandomChar(12) : '';

    if (isset($this->_aError))
      return $this->_showCreateUserTemplate();

    elseif ($this->_oModel->create($iVerificationCode) === true) {
      $this->oSmarty->clearCacheForController($this->_aRequest['controller']);

      # Send email if user has registered and creator is not an admin.
      if ($this->_aSession['user']['role'] == 4)
        $sMailMessage = '';

      else
        $sMailMessage = I18n::get('users.mail.body',
                Helper::formatInput($this->_aRequest['name']),
                Helper::createLinkTo('users/' . $iVerificationCode . '/verification'));

      Logs::insert(  $this->_aRequest['controller'],
                    $this->_aRequest['action'],
                    $this->_oModel->getLastInsertId('users'),
                    $this->_aSession['user']['id']);

      $sMails = $this->__autoload('Mails');
      $sMails::send( Helper::formatInput($this->_aRequest['email']),
                  I18n::get('users.mail.subject'),
                  $sMailMessage,
                  WEBSITE_MAIL_NOREPLY);

      return $this->_aSession['user']['role'] == 4 ?
              Helper::successMessage(I18n::get('success.create'), '/' . $this->_aRequest['controller']) :
              Helper::successMessage(I18n::get('success.user.create'), '/');
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), '/');
  }

  /**
   * Build form template to create a user.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showCreateUserTemplate($bShowCaptcha) {
    $sTemplateDir   = Helper::getTemplateDir($this->_aRequest['controller'], 'create');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'create');

    if ($this->_aSession['user']['role'] == 4) {
      $this->setTitle(I18n::get('users.title.create'));

      $this->oSmarty->assign('name', isset($this->_aRequest['name']) ?
                      Helper::formatInput($this->_aRequest['name']) :
                      '');

      $this->oSmarty->assign('surname', isset($this->_aRequest['surname']) ?
                      Helper::formatInput($this->_aRequest['surname']) :
                      '');

      $this->oSmarty->assign('email', isset($this->_aRequest['email']) ?
                      Helper::formatInput($this->_aRequest['email']) :
                      '');
    }
    else {
      $this->setTitle(I18n::get('global.registration'));
      $this->setDescription(I18n::get('users.description.create'));

      if ($bShowCaptcha)
        $this->oSmarty->assign('_captcha_', Recaptcha::getInstance()->show());

      $this->oSmarty->assign('name', isset($this->_aRequest['name']) ?
                      Helper::formatInput($this->_aRequest['name']) :
                      $this->_aSession['user']['name']);

      $this->oSmarty->assign('surname', isset($this->_aRequest['surname']) ?
                      Helper::formatInput($this->_aRequest['surname']) :
                      $this->_aSession['user']['surname']);

      $this->oSmarty->assign('email', isset($this->_aRequest['email']) ?
                      Helper::formatInput($this->_aRequest['email']) :
                      $this->_aSession['user']['email']);
    }

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Update an user.
   *
   * Update entry or show form template if we have enough rights.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function update() {
    if ($this->_aSession['user']['id'] == 0)
      return Helper::errorMessage(I18n::get('error.session.create_first'), '/sessions/create');

    elseif ($this->_aSession['user']['id'] !== $this->_iId && $this->_aSession['user']['role'] < 4)
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

    else
      return isset($this->_aRequest['update_users']) ? $this->_update() : $this->_showFormTemplate();
  }

  /**
   * Update a user.
   *
   * Activate model, insert data into the database and redirect afterwards.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _update() {
    $this->_setError('name');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->update((int) $this->_iId) === true) {
      $this->oSmarty->clearCacheForController($this->_aRequest['controller']);

      # Check if user wants to unsubscribe from mailchimp
      if (!isset($this->_aRequest['receive_newsletter']))
        $this->_unsubscribeFromNewsletter(Helper::formatInput(($this->_aRequest['email'])));

      else
        $this->_subscribeToNewsletter($this->_aRequest);

      $this->_iId = isset($this->_aRequest['id']) && $this->_aRequest['id'] !== $this->_aSession['user']['id'] ?
              (int) $this->_aRequest['id'] :
              $this->_aSession['user']['id'];

      Logs::insert($this->_aRequest['controller'],
                  $this->_aRequest['action'],
                  (int) $this->_iId,
                  $this->_aSession['user']['id']);

      return Helper::successMessage(I18n::get('success.update'), '/' . $this->_aRequest['controller'] . '/' . $this->_iId);
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), '/' . $this->_aRequest['controller'] . '/' . $this->_iId);
  }

  /**
   * Delete a user account.
   *
   * @access public
   * @return boolean status message
   *
   */
  public function destroy() {
    return (isset($this->_aRequest['destroy_users']) && $this->_aSession['user']['id'] == $this->_iId) ||
              $this->_aSession['user']['role'] == 4 ?
            $this->_destroy() :
            Helper::errorMessage(I18n::get('error.missing.permission'), '/');
  }

  /**
   * Delete a user account.
   *
   * Check if the ids match or if the user is admin,
   * delete the user from database and redirect afterwards
   *
   * @access protected
   * @return boolean status message
   *
   */
  protected function _destroy() {
    require PATH_STANDARD . '/vendor/candyCMS/core/helpers/Upload.helper.php';
    $aUser = $this->_oModel->getUserNamesAndEmail($this->_iId);

    # is form submit and do ids match?
    if (isset($this->_aRequest['destroy_users']) && $this->_aSession['user']['id'] == $this->_iId) {
      $bCorrectPassword = md5(RANDOM_HASH . $this->_aRequest['password']) === $this->_aSession['user']['password'];
      $sSuccessRedirectUrl = '/';
      $sFailureRedirectUrl = '/' . $this->_aRequest['controller'] . '/' . $this->_aSession['user']['id'] . '/update#user-destroy';
    }
    # admin can delete everybody
    else if ($this->_aSession['user']['role'] == 4) {
      $bCorrectPassword = true;
      $sSuccessRedirectUrl = '/' . $this->_aRequest['controller'];
      $sFailureRedirectUrl = $sSuccessRedirectUrl;
    }
    # No admin and not the active user
    else
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

    if ($bCorrectPassword === true) {
      if ($this->_oModel->destroy($this->_iId) === true) {
        $this->oSmarty->clearCacheForController($this->_aRequest['controller']);

        # Unsubscribe from newsletter
        $this->_unsubscribeFromNewsletter($aUser['email']);

        # Destroy profile image
        Upload::destroyAvatarFiles($this->_iId);

        return Helper::successMessage(I18n::get('success.destroy'), $sSuccessRedirectUrl);
      }
      else
        return Helper::errorMessage(I18n::get('error.sql'), $sFailureRedirectUrl);
    }
    else
      return Helper::errorMessage(I18n::get('error.user.destroy.password'), $sFailureRedirectUrl);
  }

  /**
   * Verify email address.
   *
   * @access public
   * @return boolean status of message
   *
   */
  public function verifyEmail() {
    if (!isset($this->_aRequest['code']) || empty($this->_aRequest['code']))
      return Helper::errorMessage(I18n::get('error.missing.id'), '/');

    elseif ($this->_oModel->verifyEmail($this->_aRequest['code']) === true) {
      # Subscribe to MailChimp after email address is confirmed
      $this->_subscribeToNewsletter($this->_oModel->getActivationData());

      $this->oSmarty->clearCacheForController('users');

      return Helper::successMessage(I18n::get('success.user.verification'), '/');
    }

    else
      return Helper::errorMessage(I18n::get('error.user.verification'), '/');
  }

  /**
   * Get the API token of a user.
   *
   * @access public
   * @return string token or null
   *
   */
  public function getToken() {
    $this->_setError('email');
    $this->_setError('password');

    if (!$this->_aError)
      $sToken = $this->_oModel->getToken();

    if (isset($sToken) && $sToken)
      return json_encode(array('success' => true, 'token' => $sToken));

    else
      return json_encode(array('success' => false));
  }
}
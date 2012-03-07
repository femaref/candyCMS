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

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Helper\Upload as Upload;
use CandyCMS\Model\User as Model;
use MCAPI;
use Smarty;

class User extends Main {

	/**
	 * Include the user model.
	 *
	 * @access public
	 *
	 */
	public function __init() {
    require_once PATH_STANDARD . '/app/models/User.model.php';
		$this->_oModel = new Model($this->_aRequest, $this->_aSession, $this->_aFile);
	}

  /**
   * Route to right action.
   *
   * @access public
   * @return string HTML
   *
   */
  public function show() {
    if (isset($this->_aRequest['action'])) {
      switch ($this->_aRequest['action']) {

        case 'avatar':

          $this->setDescription(I18n::get('user.title.avatar'));
          $this->setTitle(I18n::get('user.title.avatar'));
          return $this->updateAvatar();

          break;

        case 'password':

          $this->setDescription(I18n::get('user.title.password'));
          $this->setTitle(I18n::get('user.title.password'));
          return $this->updatePassword();

          break;

        case 'token':

          $this->setDescription(I18n::get('global.api_token'));
          $this->setTitle(I18n::get('global.api_token'));
          return $this->getToken();

          break;

        case 'verification':

          $this->setDescription(I18n::get('global.email.verification'));
          $this->setTitle(I18n::get('global.email.verification'));
          return $this->verifyEmail();

          break;
      }
    }
    else
      return $this->_show();
  }

	/**
	 * Show user or user overview.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
	protected function _show() {
		if (empty($this->_iId)) {
			$this->setTitle(I18n::get('user.title.overview'));
			$this->setDescription(I18n::get('user.title.overview'));

			if ($this->_aSession['userdata']['role'] < 3)
				return Helper::errorMessage(I18n::get('error.missing.permission'), '/');

			else {
        $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'overview');
        $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'overview');

        $this->oSmarty->assign('user', $this->_oModel->getData());

        $this->oSmarty->setTemplateDir($sTemplateDir);
        return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
			}
		}
		else {
			$sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'show');
			$sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

			$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
      $this->oSmarty->setTemplateDir($sTemplateDir);

      if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
        $aData = & $this->_oModel->getData($this->_iId);
        $this->oSmarty->assign('user', $aData);

        $this->setTitle($aData[1]['full_name']);
        $this->setDescription($aData[1]['full_name']);
      }

      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
		}
	}

	/**
	 * Build form template to create or update a user.
	 *
	 * @access protected
	 * @return string HTML content
   * @todo set title and description
	 *
	 */
	protected function _showFormTemplate($bUseRequest = false) {
		# Avoid URL manipulation
		if ($this->_iId !== $this->_aSession['userdata']['id'] && $this->_aSession['userdata']['role'] < 4) {
			Helper::redirectTo('/user/update');
			exit();
		}

		# Set user id of person to update
		$iId =	$this->_iId !== $this->_aSession['userdata']['id'] && $this->_aSession['userdata']['role'] == 4 ?
            $this->_iId :
            $this->_aSession['userdata']['id'];

		# Fetch data from database
		$aData = & $this->_oModel->getData($iId, false, true);

		# Override if we want to use request
		if ($bUseRequest == true) {
			foreach ($aData as $sColumn => $sData)
				$aData[$sColumn] = isset($this->_aRequest[$sColumn]) ? $this->_aRequest[$sColumn] : $sData;
		}

		foreach ($aData as $sColumn => $sData)
			$this->oSmarty->assign($sColumn, $sData);

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

		$this->oSmarty->assign('uid', $iId);

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

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
    require PATH_STANDARD . '/app/helpers/Upload.helper.php';

    $oUpload = new Upload($this->_aRequest, $this->_aSession, $this->_aFile);
    $this->_setError('terms', I18n::get('error.file.upload'));

    if (!isset($this->_aFile['image']))
      $this->_aError['image'] = I18n::get('error.form.missing.file');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($oUpload->uploadAvatarFile(false) === true)
      return Helper::successMessage(I18n::get('success.upload'), '/user/' . $this->_iId);

    else
      return Helper::errorMessage(I18n::get('error.file.upload'), '/user/' . $this->_iId);
  }

	/**
	 * Update a users password.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
  public function updatePassword() {
    # Check if old password is set
    if (empty($this->_aRequest['password_old']))
      $this->_aError['password_old'] = I18n::get('error.user.update.password.old.empty');

    # Check if new password fields aren't empty
    if (empty($this->_aRequest['password_new']) || empty($this->_aRequest['password_new2']))
      $this->_aError['password_new'] = I18n::get('error.user.update.password.new.empty');

    # Check if old password is correct
    if (!empty($this->_aRequest['password_old']) &&
            md5(RANDOM_HASH . $this->_aRequest['password_old']) !==
            $this->_aSession['userdata']['password'])
      $this->_aError['password_old'] = I18n::get('error.user.update.password.old.wrong');

    # Check if new password fields match
    if ($this->_aRequest['password_new'] !== $this->_aRequest['password_new2'])
      $this->_aError['password_new'] = I18n::get('error.user.update.password.new.match');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->updatePassword((int) $this->_iId) === true) {
      $this->_iId = isset($this->_aRequest['id']) && $this->_aRequest['id'] !== $this->_aSession['userdata']['id'] ?
              (int) $this->_aRequest['id'] :
              $this->_aSession['userdata']['id'];

      Log::insert($this->_aRequest['section'],
									$this->_aRequest['action'],
									(int) $this->_iId,
									$this->_aSession['userdata']['id']);

      return Helper::successMessage(I18n::get('success.update'), '/user/' . $this->_iId);
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), '/user/' . $this->_iId);
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
		return isset($this->_aRequest['create_user']) ? $this->_create() : $this->_showCreateUserTemplate();
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
	protected function _create() {
		$this->_setError('name');
		$this->_setError('surname');
		$this->_setError('email');
		$this->_setError('password');

		if (Model::getExistingUser($this->_aRequest['email']))
			$this->_aError['email'] = I18n::get('error.user.create.email');

		if ($this->_aRequest['password'] !== $this->_aRequest['password2'])
			$this->_aError['password'] = I18n::get('error.passwords');

		# Admin does not need to confirm disclaimer
		if ($this->_aSession['userdata']['role'] < 4 && !isset($this->_aRequest['disclaimer']))
      $this->_aError['disclaimer'] = I18n::get('error.form.missing.terms');

    # Generate verification code for users (double-opt-in) when not created by admin.
    $iVerificationCode = $this->_aSession['userdata']['role'] < 4 ? Helper::createRandomChar(12) : '';

		if (isset($this->_aError))
			return $this->_showCreateUserTemplate();

    elseif ($this->_oModel->create($iVerificationCode) === true) {
      $this->__autoload('Mail');

      $iUserId = $this->_oModel->getLastInsertId('users');

      # Send email if user has registered and creator is not an admin.
      if ($this->_aSession['userdata']['role'] == 4)
        $sMailMessage = '';

			else {
        $sVerificationUrl = Helper::createLinkTo('user/' . $iVerificationCode . '/verification');

        $sMailMessage = str_replace('%u', Helper::formatInput($this->_aRequest['name']), I18n::get('user.mail.body'));
        $sMailMessage = str_replace('%v', $sVerificationUrl, $sMailMessage);
      }

			Log::insert($this->_aRequest['section'],
									$this->_aRequest['action'],
									$iUserId,
									$this->_aSession['userdata']['id']);

			Mail::send(	Helper::formatInput($this->_aRequest['email']),
									I18n::get('user.mail.subject'),
									$sMailMessage,
									WEBSITE_MAIL_NOREPLY);

      return $this->_aSession['userdata']['role'] == 4 ?
              Helper::successMessage(I18n::get('success.create'), '/user') :
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
	protected function _showCreateUserTemplate() {
		$this->oSmarty->assign('name', isset($this->_aRequest['name']) ?
										Helper::formatInput($this->_aRequest['name']) :
										'');

		$this->oSmarty->assign('surname', isset($this->_aRequest['surname']) ?
										Helper::formatInput($this->_aRequest['surname']) :
										'');

		$this->oSmarty->assign('email', isset($this->_aRequest['email']) ?
										Helper::formatInput($this->_aRequest['email']) :
										'');

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'create');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'create');

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
    if ($this->_iId > 0 && $this->_aSession['userdata']['id'] == $this->_iId && !isset($this->_aRequest['update_user']))
      Helper::redirectTo('/user/update');

    if (empty($this->_iId))
      $this->_iId = $this->_aSession['userdata']['id'];

    if ($this->_aSession['userdata']['id'] == 0)
      return Helper::errorMessage(I18n::get('error.session.create_first'), '/');

    else
      return isset($this->_aRequest['update_user']) ? $this->_update() : $this->_showFormTemplate();
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
			$this->oSmarty->clearCache(null, $this->_aRequest['section']);

      # Check if user wants to unsubscribe from mailchimp
			if (!isset($this->_aRequest['receive_newsletter']))
				$this->_unsubscribeFromNewsletter(Helper::formatInput(($this->_aRequest['email'])));

			else
				$this->_subscribeToNewsletter($this->_aRequest);

			$this->_iId = isset($this->_aRequest['id']) && $this->_aRequest['id'] !== $this->_aSession['userdata']['id'] ?
              (int) $this->_aRequest['id'] :
              $this->_aSession['userdata']['id'];

			Log::insert($this->_aRequest['section'],
									$this->_aRequest['action'],
									(int) $this->_iId,
									$this->_aSession['userdata']['id']);

			return Helper::successMessage(I18n::get('success.update'), '/user/' . $this->_iId);
		}
		else
			return Helper::errorMessage(I18n::get('error.sql'), '/user/' . $this->_iId);
	}

	/**
	 * Destroy user avatar images.
	 *
	 * @access private
	 * @param integer $iId user id.
	 *
	 */
	private function _destroyUserAvatars($iId) {
		@unlink(Helper::removeSlash(PATH_UPLOAD . '/user/32/' . (int) $iId . '.jpg'));
		@unlink(Helper::removeSlash(PATH_UPLOAD . '/user/64/' . (int) $iId . '.jpg'));
		@unlink(Helper::removeSlash(PATH_UPLOAD . '/user/100/' . (int) $iId . '.jpg'));
		@unlink(Helper::removeSlash(PATH_UPLOAD . '/user/200/' . (int) $iId . '.jpg'));
		@unlink(Helper::removeSlash(PATH_UPLOAD . '/user/popup/' . (int) $iId . '.jpg'));
		@unlink(Helper::removeSlash(PATH_UPLOAD . '/user/original/' . (int) $iId . '.jpg'));
	}

	/**
	 * Delete a user account.
	 *
	 * @access public
	 * @return boolean status message
	 *
	 */
	public function destroy() {
    $aUser = MODEL::getUserNamesAndEmail($this->_iId);

    # We are a user and want to delete our account
    if (isset($this->_aRequest['destroy_user']) && $this->_aSession['userdata']['id'] == $this->_iId) {
      # Password matches with user password
      if (md5(RANDOM_HASH . $this->_aRequest['password']) === $this->_aSession['userdata']['password']) {
        if ($this->_oModel->destroy($this->_iId) === true) {
					$this->oSmarty->clearCache(null, $this->_aRequest['section']);

          # Unsubscribe from newsletter
          $this->_unsubscribeFromNewsletter($aUser['email']);

          # Destroy profile image
          $this->_destroyUserAvatars($this->_iId);
          return Helper::successMessage(I18n::get('success.destroy'), '/');
        }
        else
          return Helper::errorMessage(I18n::get('error.sql'), '/user/update');

      } else
        return Helper::errorMessage(I18n::get('error.user.destroy.password'), '/user/update#user-destroy');

      # We are admin and can delete users
    } elseif ($this->_aSession['userdata']['role'] == 4) {
      if ($this->_oModel->destroy($this->_iId) === true) {
				$this->oSmarty->clearCache(null, $this->_aRequest['section']);

        # Unsubscribe from newsletter
        $this->_unsubscribeFromNewsletter($aUser['email']);

        # Destroy profile image
        $this->_destroyUserAvatars($this->_iId);

        Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_iId, $this->_aSession['userdata']['id']);
        return Helper::successMessage(I18n::get('success.destroy'), '/user');
      }
      else
        return Helper::errorMessage(I18n::get('error.sql'), '/user');
    }

    # No admin and not the active user
    else
      return Helper::errorMessage(I18n::get('error.missing.permission'), '/');
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
			# Get data from activating user
			$aUserData = & $this->_oModel->getActivationData();

			# Subscribe to MailChimp after email adress is confirmed
			$this->_subscribeToNewsletter($aUserData);

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

    return !isset($this->_aError) ? $this->_oModel->getToken() : json_encode(array('success', false));
  }
}

<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/controllers/Session.controller.php';
require_once 'app/controllers/Mail.controller.php';
require_once 'app/models/User.model.php';
require_once 'app/helpers/Image.helper.php';
require_once 'app/helpers/Upload.helper.php';

class User extends Main {

  public final function __init() {
    $this->_oModel = new Model_User($this->m_aRequest, $this->m_oSession, $this->m_aFile);
  }

  # @Override

  public function update() {
    if (empty($this->_iID))
      $this->_iID = USER_ID;

    if (USER_ID == 0)
      return Helper::errorMessage(LANG_ERROR_LOGIN_FIRST, LANG_ERROR_GLOBAL_NO_PERMISSION);
    else {
      if (isset($this->m_aRequest['update_user']))
        return $this->_update($this->_iID);
      elseif (isset($this->m_aRequest['create_avatar']))
        return $this->_createAvatar($this->_iID);
      else
        return $this->_showFormTemplate();
    }
  }

  private function _createAvatar() {
    $iAgreement = isset($this->m_aRequest['agreement']) ? 1 : 0;

    if ($iAgreement == 0)
      return Helper::errorMessage(LANG_ERROR_USER_SETTINGS_UPLOAD_AGREEMENT) .
      $this->_showFormTemplate();
    else {
      $oUpload = new Upload($this->m_aRequest, $this->m_aFile);
      return $oUpload->uploadAvatarFile(false) .
      $this->show($this->_iID);
    }
  }

  protected function _showFormTemplate($bUseRequest = false) {
    $oSmarty = new Smarty();
    if ($this->_iID !== USER_ID && USER_RIGHT == 4) {
      $this->_aData = $this->_oModel->getData($this->_iID);

      $aGravatar = array('use_gravatar' => (int)$this->_aData['use_gravatar'],
          'email' => $this->_aData['email']);

      $oSmarty->assign('uid',
              $this->_iID);
      $oSmarty->assign('name',
              $this->_aData['name']);
      $oSmarty->assign('surname',
              $this->_aData['surname']);
      $oSmarty->assign('email',
              $this->_aData['email']);
      $oSmarty->assign('description',
              $this->_aData['description']);
      $oSmarty->assign('use_gravatar',
              (int)$this->_aData['use_gravatar']);
      $oSmarty->assign('newsletter_default',
              (int)$this->_aData['newsletter_default']);
      $oSmarty->assign('userright',
              (int)$this->_aData['userright']);
    } else {
      # Avoid redisplay-Bug
      if ($bUseRequest == true) {
        $this->m_oSession['userdata']['name'] = & $this->m_aRequest['name'];
        $this->m_oSession['userdata']['surname'] = & $this->m_aRequest['surname'];
        $this->m_oSession['userdata']['email'] = & $this->m_aRequest['email'];
        $this->m_oSession['userdata']['description'] = & $this->m_aRequest['description'];
        $this->m_oSession['userdata']['newsletter_default'] = & $this->m_aRequest['newsletter_default'];
        $this->m_oSession['userdata']['use_gravatar'] = & $this->m_aRequest['use_gravatar'];
      }

      $aGravatar = array('use_gravatar' => (int) $this->m_oSession['userdata']['use_gravatar'],
          'email' => $this->m_oSession['userdata']['email']);

      $oSmarty->assign('uid', USER_ID);
      $oSmarty->assign('name',
              $this->m_oSession['userdata']['name']);
      $oSmarty->assign('surname',
              $this->m_oSession['userdata']['surname']);
      $oSmarty->assign('email',
              $this->m_oSession['userdata']['email']);
      $oSmarty->assign('description',
              $this->m_oSession['userdata']['description']);
      $oSmarty->assign('newsletter_default',
              (int) $this->m_oSession['userdata']['newsletter_default']);
      $oSmarty->assign('use_gravatar',
              (int) $this->m_oSession['userdata']['use_gravatar']);

      # Avoid Smarty Bug if you're an administrator
      $oSmarty->assign('USER_RIGHT', USER_RIGHT);
    }

    $oSmarty->assign('avatar_100', Helper::getAvatar('user', 100, $this->_iID, $aGravatar));
    $oSmarty->assign('avatar_popup', Helper::getAvatar('user', POPUP_DEFAULT_X, $this->_iID, $aGravatar));

    # Set Form params
    $oSmarty->assign('action', '/User/Settings');
    $oSmarty->assign('style', 'display:none');

    # Set _own_ USER_RIGHT and USER_ID for updating purposes
    $oSmarty->assign('USER_ID', USER_ID);
    $oSmarty->assign('USER_RIGHT', USER_RIGHT);

    # Compress slimbox
    $oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '-min' : '');

    # Language
    $oSmarty->assign('lang_about_you', LANG_USER_SETTINGS_ABOUT_YOU);
    $oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
    $oSmarty->assign('lang_headline', LANG_USER_SETTINGS_HEADLINE);
    $oSmarty->assign('lang_image_agreement', LANG_USER_SETTINGS_IMAGE_AGREEMENT);
    $oSmarty->assign('lang_image_change', LANG_USER_SETTINGS_IMAGE_CHANGE);
    $oSmarty->assign('lang_image_choose', LANG_USER_SETTINGS_IMAGE_CHOOSE);
    $oSmarty->assign('lang_image_headline', LANG_USER_SETTINGS_IMAGE_CHOOSE);
    $oSmarty->assign('lang_image_gravatar_info', LANG_USER_SETTINGS_IMAGE_GRAVATAR_INFO);
    $oSmarty->assign('lang_image_upload', LANG_USER_SETTINGS_IMAGE_UPLOAD);
    $oSmarty->assign('lang_image_upload_info', LANG_USER_SETTINGS_IMAGE_UPLOAD_INFO);
    $oSmarty->assign('lang_name', LANG_GLOBAL_NAME);
    $oSmarty->assign('lang_newsletter', LANG_USER_SETTINGS_NEWSLETTER);
    $oSmarty->assign('lang_password_change', LANG_USER_SETTINGS_PASSWORD_CHANGE);
    $oSmarty->assign('lang_password_new', LANG_USER_SETTINGS_PASSWORD_NEW);
    $oSmarty->assign('lang_password_old', LANG_USER_SETTINGS_PASSWORD_OLD);
    $oSmarty->assign('lang_password_repeat', LANG_GLOBAL_PASSWORD_REPEAT);
    $oSmarty->assign('lang_required', LANG_GLOBAL_REQUIRED);
    $oSmarty->assign('lang_submit', LANG_USER_SETTINGS_SUBMIT);
    $oSmarty->assign('lang_surname', LANG_GLOBAL_SURNAME);
    $oSmarty->assign('lang_use_gravatar', LANG_USER_SETTINGS_IMAGE_USE_GRAVATAR);
    $oSmarty->assign('lang_userright', LANG_GLOBAL_USERRIGHT);
    $oSmarty->assign('lang_userright_1', LANG_GLOBAL_USERRIGHT_1);
    $oSmarty->assign('lang_userright_2', LANG_GLOBAL_USERRIGHT_2);
    $oSmarty->assign('lang_userright_3', LANG_GLOBAL_USERRIGHT_3);
    $oSmarty->assign('lang_userright_4', LANG_GLOBAL_USERRIGHT_4);

    $oSmarty->template_dir = Helper::getTemplateDir('user/_form');
    return $oSmarty->fetch('user/_form.tpl') .
    $oSmarty->fetch('user/createAvatar.tpl');
  }

  protected function _update() {
    $sError = '';

    if (!isset($this->m_aRequest['name']) ||
            empty($this->m_aRequest['name']))
      $sError .= LANG_GLOBAL_NAME . '<br />';

    if (!isset($this->m_aRequest['email']) ||
            empty($this->m_aRequest['email']))
      $sError .= LANG_GLOBAL_EMAIL . '<br />';

    if (empty($this->m_aRequest['oldpw']) &&
            !empty($this->m_aRequest['newpw']) &&
            !empty($this->m_aRequest['newpw2']))
      $sError .= LANG_ERROR_USER_SETTINGS_PW_OLD . '<br />';

    if (!empty($this->m_aRequest['oldpw']) &&
            md5(RANDOM_HASH . $this->m_aRequest['oldpw']) !==
            $this->m_oSession['userdata']['password'])
      $sError .= LANG_ERROR_USER_SETTINGS_PW_OLD_WRONG . '<br />';

    if (!empty($this->m_aRequest['oldpw']) && (
            empty($this->m_aRequest['newpw']) ||
            empty($this->m_aRequest['newpw2']) ))
      $sError .= LANG_ERROR_USER_SETTINGS_PW_NEW . '<br />';

    if (isset($this->m_aRequest['newpw']) && isset($this->m_aRequest['newpw2']) &&
            $this->m_aRequest['newpw'] !== $this->m_aRequest['newpw2'])
      $sError .= LANG_ERROR_USER_SETTINGS_PW_NEW_WRONG . '<br />';

    if (!empty($sError)) {
      $sReturn = Helper::errorMessage($sError);
      $sReturn .= $this->_showFormTemplate();
      return $sReturn;
    } else {

      # Fix for missing id
      $this->_iID = isset($this->m_aRequest['id']) && $this->m_aRequest['id'] !== USER_ID ?
              (int) $this->m_aRequest['id'] :
              USER_ID;

      if ($this->_oModel->update($this->_iID) == true)
        return Helper::successMessage(LANG_SUCCESS_UPDATE) .
        $this->_showFormTemplate(true);
      else
        return Helper::errorMessage(LANG_ERROR_DB_QUERY);
    }
  }

  public function show($iUID = '') {
    # Fix to avoid empty UID on /User/Settings shortcut
    if (!empty($iUID))
      $this->_iID = (int) $iUID;

    $oSmarty = new Smarty();
    $oSmarty->assign('USER_RIGHT', USER_RIGHT);

    # System variables
    $oSmarty->assign('_compress_files_suffix_', WEBSITE_COMPRESS_FILES == true ? '-min' : '');

    # Language
    $oSmarty->assign('lang_last_login', LANG_USER_LAST_LOGIN);
    $oSmarty->assign('lang_registered_since', LANG_USER_REGISTERED_SINCE);

    if (!empty($this->_iID)) {

      $this->_aData = $this->_oModel->getData($this->_iID);
      $aGravatar = array('use_gravatar' => $this->_aData['use_gravatar'],
          'email' => $this->_aData['email']);

      # Description Fix, format Code to BB
      $this->_aData['description'] = Helper::formatOutput($this->_aData['description']);

      $oSmarty->assign('uid', $this->_iID);
      $oSmarty->assign('last_login', Helper::formatTimestamp($this->_aData['last_login']));
      $oSmarty->assign('regdate', Helper::formatTimestamp($this->_aData['regdate']));
      $oSmarty->assign('user', $this->_aData);
      $oSmarty->assign('avatar_100', Helper::getAvatar('user', 100, $this->_iID, $aGravatar));
      $oSmarty->assign('avatar_popup', Helper::getAvatar('user', POPUP_DEFAULT_X, $this->_iID, $aGravatar));

      # Manage PageTitle
      $this->_sName = $this->_aData['name'];
      $this->_setTitle($this->_sName . ' ' . $this->_aData['surname']);

      # Language
      $oSmarty->assign('lang_about_himself', str_replace('%u', $this->_sName, LANG_USER_ABOUT_HIMSELF));
      $oSmarty->assign('lang_contact', LANG_GLOBAL_CONTACT);
      $oSmarty->assign('lang_contact_via_mail', str_replace('%u', $this->_sName, LANG_USER_CONTACT_VIA_EMAIL));
      $oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);


      $oSmarty->template_dir = Helper::getTemplateDir('user/show');
      return $oSmarty->fetch('user/show.tpl');
    } else {
      $this->_setTitle(LANG_USER_OVERVIEW);

      if (USER_RIGHT < 3)
        return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
      else {
        $this->_aData = $this->_oModel->getData();
        $oSmarty->assign('user', $this->_aData);

        # Language
        $oSmarty->assign('lang_create', LANG_USER_CREATE);
        $oSmarty->assign('lang_destroy', LANG_GLOBAL_DESTROY);
        $oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);
        $oSmarty->assign('lang_headline', LANG_GLOBAL_USERMANAGER);

        $oSmarty->template_dir = Helper::getTemplateDir('user/overview');
        return $oSmarty->fetch('user/overview.tpl');
      }
    }
  }

  # @Override

  public function destroy() {
    if (USER_RIGHT == 4) {
      if ($this->_oModel->destroy($this->_iID) == true)
        return Helper::successMessage(LANG_SUCCESS_DESTROY) .
        $this->show();
      else
        return Helper::errorMessage(LANG_ERROR_DB_QUERY);
    }
    else
      return Helper::errorMessage(LANG_ERROR_GLOBAL_NO_PERMISSION);
  }

  public function create() {
    if (isset($this->m_aRequest['create_user']))
      return $this->_create();
    else
      return $this->_showCreateUserTemplate();
  }

  private function _showCreateUserTemplate() {
    $oSmarty = new Smarty();

    $oSmarty->assign('USER_RIGHT', USER_RIGHT);

    $sName = isset($this->m_aRequest['name']) ? Helper::formatInput($this->m_aRequest['name']) : '';
    $oSmarty->assign('name', $sName);
    $sSurname = isset($this->m_aRequest['surname']) ? Helper::formatInput($this->m_aRequest['surname']) : '';
    $oSmarty->assign('surname', $sSurname);
    $sEmail = isset($this->m_aRequest['email']) ? Helper::formatInput($this->m_aRequest['email']) : '';
    $oSmarty->assign('email', $sEmail);

    # AJAX reload disclaimer
    $oSmarty->assign('_public_folder_', WEBSITE_CDN . '/public/images');

    # Language
    $oSmarty->assign('lang_disclaimer_read', LANG_LOGIN_REGISTRATION_DISCLAIMER_READ);
    $oSmarty->assign('lang_email', LANG_GLOBAL_EMAIL);
    $oSmarty->assign('lang_headline', LANG_GLOBAL_REGISTRATION);
    $oSmarty->assign('lang_name', LANG_GLOBAL_NAME);
    $oSmarty->assign('lang_optional', LANG_GLOBAL_OPTIONAL);
    $oSmarty->assign('lang_password', LANG_GLOBAL_PASSWORD);
    $oSmarty->assign('lang_password_repeat', LANG_GLOBAL_PASSWORD_REPEAT);
    $oSmarty->assign('lang_submit', LANG_GLOBAL_REGISTER);
    $oSmarty->assign('lang_surname', LANG_GLOBAL_SURNAME);

    $oSmarty->template_dir = Helper::getTemplateDir('user/createUser');
    return $oSmarty->fetch('user/createUser.tpl');
  }

  private function _create() {
    $sError = '';
    if (empty($this->m_aRequest['name']))
      $sError .= LANG_ERROR_LOGIN_ENTER_NAME . '<br />';

    if (empty($this->m_aRequest['email']))
      $sError .= LANG_ERROR_LOGIN_ENTER_EMAIL . '<br />';

    if (empty($this->m_aRequest['password']))
      $sError .= LANG_ERROR_LOGIN_ENTER_PASSWORD . '<br />';

    if ($this->m_aRequest['password'] !== $this->m_aRequest['password2'])
      $sError .= LANG_ERROR_LOGIN_CHECK_PASSWORDS . '<br />';

    if (USER_RIGHT < 4) {
      if (!isset($this->m_aRequest['disclaimer']))
        $sError .= LANG_ERROR_LOGIN_CHECK_DISCLAIMER . '<br />';
    }

    if (Helper::checkEmailAddress($this->m_aRequest['email']) == false)
      $sError .= LANG_ERROR_WRONG_EMAIL_FORMAT . '<br />';

    if (!empty($sError)) {
      $sReturn = Helper::errorMessage($sError);
      $sReturn .= $this->_showCreateUserTemplate();
      return $sReturn;
    } else {
      # Switch to user model
      # @Override Model
      # NOTE: Dirty method, no OO used
      $this->_oController = new Session($this->m_aRequest, $this->m_oSession);
      $this->_oModel = new Model_User($this->m_aRequest, $this->m_oSession);

      if ($this->_oModel->create() == true) {
        $sMail = str_replace('%n', Helper::formatInput($this->m_aRequest['name']),
                        LANG_LOGIN_REGISTRATION_MAIL_BODY);

        $bStatus = Mail::send(Helper::formatInput($this->m_aRequest['email']),
                        LANG_LOGIN_REGISTRATION_MAIL_SUBJECT,
                        $sMail,
                        WEBSITE_MAIL_NOREPLY);

        if ($bStatus == true)
          return Helper::successMessage(LANG_LOGIN_REGISTRATION_SUCCESSFUL) .
          $this->_oController->showCreateSessionTemplate();
        else
          return Helper::errorMessage(LANG_ERROR_MAIL_FAILED_SUBJECT);
      }
      else
        return Helper::errorMessage(LANG_ERROR_DB_QUERY);
    }
  }

}
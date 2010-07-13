<?php

/*
 * This software is copyright protected. Use only allowed on licensed
 * websites. Contact author for further information or to receive a license.
 *
 * @link http://marcoraddatz.com
 * @copyright 2007 - 2008 Marco Raddatz
 * @author Marco Raddatz <mr at marcoraddatz dot com>
 * @package CMS
 * @version 1.0
*/

class Model_Login extends Model_Main {
  public final function createSession() {
    $oCheckUser = new Query("	SELECT
																*
															FROM
																user
															WHERE
																email = '"	.Helper::formatHTMLCode($this->m_aRequest['email']).	"'
															AND
																password = MD5('"	.RANDOM_HASH.Helper::formatHTMLCode($this->m_aRequest['password']).	"')
															LIMIT 1");

    # Check if user exists
    if($oCheckUser->numRows() == 1) {
      $this->_aData =& $oCheckUser->fetch();

      new Query("	UPDATE
										user
									SET
										session = '"	.session_id().	"',
										ip = '"	.$_SERVER['REMOTE_ADDR'].	"',
										last_login = '"	.time().	"'
									WHERE
										id = "	.(int)$this->_aData['id']);

      if(empty($this->_aData['last_login'])) {
        return Helper::successMessage(LANG_LOGIN_LOGIN_SUCCESSFUL).
                Helper::redirectTo('/User/Settings');
      }
      else {
        return Helper::successMessage(LANG_LOGIN_LOGIN_SUCCESSFUL).
                Helper::redirectTo('/Start');
      }
    }
    else {
      $oController = new Login($this->m_aRequest, $this->m_oSession);
      return Helper::errorMessage(LANG_ERROR_LOGIN_WRONG_USERDATA, LANG_ERROR_LOGIN_HEADLINE).
              $oController->showCreateSessionTemplate();
    }
  }

  # TODO: Clean up Model and Controller
  public final function createNewPassword() {
    $sError = '';
    if( empty($this->m_aRequest['email']) )
      $sError .= LANG_ERROR_LOGIN_ENTER_EMAIL.	'<br />';

    $oGetUser = new Query("	SELECT
															COUNT(email)
														FROM
															user
														WHERE
															email = '"	.Helper::formatHTMLCode($this->m_aRequest['email']).	"'");

    $iUser = $oGetUser->count();
    if( $iUser == 0 )
      $sError .= LANG_ERROR_LOGIN_NO_SUCH_EMAIL.	'<br />';

    if( !empty($sError) )
      return Helper::errorMessage($sError);

    else {
      $sNewPasswordClean = Helper::createRandomChar(10);
      $sNewPasswordSecure = md5(RANDOM_HASH.$sNewPasswordClean);

      $oGetUserName = new Query("	SELECT
																		name
																	FROM
																		user
																	WHERE
																		email = '"	.Helper::formatHTMLCode($this->m_aRequest['email']).	"'
																	LIMIT
																		1");
      $aRow = $oGetUserName->fetch();

      # TODO: Put into controller?
      $sContent = str_replace('%u', $aRow['name'], LANG_LOGIN_PASSWORD_LOST_MAIL_BODY);
      $sContent = str_replace('%p', $sNewPasswordClean, $sContent);

      $sStatus = Mail::send(	Helper::formatHTMLCode($this->m_aRequest['email']),
              LANG_LOGIN_PASSWORD_LOST_MAIL_SUBJECT,
              $sContent,
              true,
              WEBSITE_MAIL_NOREPLY);

      if( $sStatus == false )
        return Helper::errorMessage(LANG_ERROR_MAIL_FAILED_SUBJECT);

      else {
        return new Query("UPDATE
														`user`
													SET
														password = '"	.$sNewPasswordSecure.	"'
													WHERE
														`email` = '"	.Helper::formatHTMLCode($this->m_aRequest['email']).	"'");
      }
    }
  }

  public final function destroySession() {
    new Query("UPDATE `user` SET `session` = '' WHERE session = '"	.session_id().	"'");
    #session_destroy();
    unset($_SESSION);
    return Helper::successMessage(LANG_LOGIN_LOGOUT_SUCCESSFUL);
  }
}
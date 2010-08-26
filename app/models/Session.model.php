<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */


class Model_Session extends Model_Main {
  # Get userdata; static function and direct return due to uncritical action
  public static final function getSessionData($iSessionId = '') {
    if (empty($iSessionId))
      $iSessionId = session_id();

    try {
			$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
			$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$oQuery = $oDb->prepare("SELECT * FROM user WHERE session = :session_id AND ip = :ip LIMIT 1");

			$oQuery->bindParam('session_id', $iSessionId);
			$oQuery->bindParam('ip', $_SERVER['REMOTE_ADDR']);
			$bReturn = $oQuery->execute();

			if ($bReturn == false)
				$this->destroy();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;

			return $aResult;
		}
		catch (AdvancedException $e) {
			$oDb->rollBack();
			$e->getMessage();
		}
  }

	public static function setActiveSession($iId) {
		try {
			$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
			$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$oQuery = $oDb->prepare("	UPDATE
																	user
																SET
																	session = :session,
																	ip = :ip,
																	last_login = :last_login
																WHERE
																	id = :id");

			$oQuery->bindParam('session', session_id());
			$oQuery->bindParam('ip', $_SERVER['REMOTE_ADDR']);
			$oQuery->bindParam('last_login', time());
			$oQuery->bindParam('id', $iId);
			$bResult = $oQuery->execute();

			$oDb = null;
			return $bResult;
		}
		catch (AdvancedException $e) {
			$oDb->rollBack();
			$e->getMessage();
		}
	}

  # Create session
  public final function create() {
		try {
			$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
			$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$oQuery = $oDb->prepare(" SELECT
																	id, verification_code
																FROM
																	user
																WHERE
																	email = :email
																AND
																	password = :password
																LIMIT
																	1");

			$sPassword = md5(RANDOM_HASH . Helper::formatInput($this->_aRequest['password']));
			$oQuery->bindParam('email', Helper::formatInput($this->_aRequest['email']));
			$oQuery->bindParam('password', $sPassword);
			$oQuery->execute();

			$aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
		}
		catch (AdvancedException $e) {
			$oDb->rollBack();
			$e->getMessage();
		}

    # Check if user exists
		if(isset($aResult) && !empty($aResult['verification_code'])) {
			return false;
		}
    elseif (!empty($aResult['id']))
			return Model_Session::setActiveSession($aResult['id']);
		else {
			$oDb = null;
			return false;
		}
  }

  public final function createResendActions() {
    # TODO: Remove mails from here
		require_once 'app/controllers/Mail.controller.php';
		$bResult = false;

    if($this->_aRequest['action'] == 'resendpassword') {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
                    PDO::ATTR_PERSISTENT => true
                ));
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare("SELECT name, email FROM user WHERE email = :email");
        $oQuery->bindParam(':email', Helper::formatInput($this->_aRequest['email']));
        $bResult = $oQuery->execute();

        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
        $e->getMessage();
      }

      if( empty($aResult['name']) || empty($bResult) || $bResult == false) {
        $oDb = null;
        return false;
      }
      else {
        $aRow = & $aResult;

        $sNewPasswordClean	= Helper::createRandomChar(10);
        $sNewPasswordSecure = md5(RANDOM_HASH . $sNewPasswordClean);

        try {
          $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
          $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $oQuery = $oDb->prepare("UPDATE user SET password = :password WHERE email = :email");
          $oQuery->bindParam(':password', $sNewPasswordSecure);
          $oQuery->bindParam(':email', Helper::formatInput($this->_aRequest['email']));

          $bResult = $oQuery->execute();
          $oDb = null;
        }
        catch (AdvancedException $e) {
          $oDb->rollBack();
          $e->getMessage();
        }

        if($bResult == true) {
          $sContent = str_replace('%u', $aRow['name'], LANG_LOGIN_PASSWORD_LOST_MAIL_BODY);
          $sContent = str_replace('%p', $sNewPasswordClean, $sContent);

          $bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
                          LANG_LOGIN_PASSWORD_LOST_MAIL_SUBJECT,
                          $sContent,
                          WEBSITE_MAIL_NOREPLY);

          return $bStatus;
        }
        else
          return false;
      }
    }
    else {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare("SELECT name, verification_code FROM user WHERE email = :email");
        $oQuery->bindParam(':email', Helper::formatInput($this->_aRequest['email']));
        $bResult = $oQuery->execute();

        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);

        if( empty($aResult['name']) || empty($bResult) || $bResult == false) {
          $oDb = null;
          return false;
        }
        else {
          if (!empty($aResult['verification_code'])) {
            $aRow = & $aResult;

            $sVerificationUrl		= Helper::createLinkTo('/User/'	.$iVerifyCode.	'/verification');

            $sContent = str_replace('%u', $aRow['name'], LANG_LOGIN_RESEND_VERIFICATION_MAIL_BODY);
            $sContent = str_replace('%v', $sVerificationUrl, $sContent);

            $bStatus = Mail::send(Helper::formatInput($this->_aRequest['email']),
                            LANG_LOGIN_RESEND_VERIFICATION_MAIL_SUBJECT,
                            $sContent,
                            WEBSITE_MAIL_NOREPLY);

            return $bStatus;
          }
          else
            return false;

          $oDb = null;
        }
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
        $e->getMessage();
      }
    }
  }

  public final function destroy() {
		try {
			$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
			$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$oQuery = $oDb->prepare("	UPDATE
                                  user
                                SET
                                  session = :session_null
                                WHERE
                                  session = :session_id");

			$sNull = 'NULL';
			$iSessionId = session_id();
			$oQuery->bindParam('session_null', $sNull, PDO::PARAM_NULL);
			$oQuery->bindParam('session_id', $iSessionId);
			$bResult = $oQuery->execute();

			$oDb = null;
			return $bResult;
		}
		catch (AdvancedException $e) {
			$oDb->rollBack();
			$e->getMessage();
		}
	}
}
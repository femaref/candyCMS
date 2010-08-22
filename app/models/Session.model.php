<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/controllers/Mail.controller.php';

class Model_Session extends Model_Main {
  public final function create() {
		try {
			$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
										PDO::ATTR_PERSISTENT => true
								));
			$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$oQuery = $oDb->prepare(" SELECT
																	id
																FROM
																	user
																WHERE
																	email = :email
																AND
																	password = :password
																LIMIT
																	1");

			$sPassword = md5(RANDOM_HASH.Helper::formatInput($this->m_aRequest['password']));
			$oQuery->bindParam('email', Helper::formatInput($this->m_aRequest['email']));
			$oQuery->bindParam('password', $sPassword);
			$oQuery->execute();

			$aResult = $oQuery->fetch(PDO::FETCH_ASSOC);

		} catch (AdvancedException $e) {
			$oDb->rollBack();
			$e->getMessage();
			die();
		}

    # Check if user exists
    if(!empty($aResult['id'])) {
      $this->_aData =& $aResult;

			try {
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
				$oQuery->bindParam('id', $this->_aData['id']);
				$bResult = $oQuery->execute();

				$oDb = null;
				return $bResult;

			} catch (AdvancedException $e) {
				$oDb->rollBack();
				$e->getMessage();
				die();
			}
    }
    else
      return false;

		$oDb = null;
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
															email = '"	.Helper::formatInput($this->m_aRequest['email']).	"'");

    $iUser = $oGetUser->count();
    if( $iUser == 0 )
      $sError .= LANG_ERROR_LOGIN_NO_SUCH_EMAIL.	'<br />';

    if( !empty($sError) )
      return Helper::errorMessage($sError);

    else {
      $sNewPasswordClean  = Helper::createRandomChar(10);
      $sNewPasswordSecure = md5(RANDOM_HASH.$sNewPasswordClean);

      $oGetUserName = new Query("	SELECT
																		name
																	FROM
																		user
																	WHERE
																		email = '"	.Helper::formatInput($this->m_aRequest['email']).	"'
																	LIMIT
																		1");

      $aRow = $oGetUserName->fetch();

      # TODO: Put into controller?
      $sContent = str_replace('%u', $aRow['name'], LANG_LOGIN_PASSWORD_LOST_MAIL_BODY);
      $sContent = str_replace('%p', $sNewPasswordClean, $sContent);

      $bStatus = Mail::send(	Helper::formatInput($this->m_aRequest['email']),
              LANG_LOGIN_PASSWORD_LOST_MAIL_SUBJECT,
              $sContent,
              WEBSITE_MAIL_NOREPLY);

      if( $bStatus == true ) {
        return new Query("UPDATE
                            `user`
                          SET
                            password = '"	.$sNewPasswordSecure.	"'
                          WHERE
                            `email` = '"	.Helper::formatInput($this->m_aRequest['email']).	"'");
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

      $sNull			= 'NULL';
      $iSessionId = session_id();
      $oQuery->bindParam('session_null', $sNull, PDO::PARAM_NULL);
      $oQuery->bindParam('session_id', $iSessionId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;

    } catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
      die();
    }
  }
}
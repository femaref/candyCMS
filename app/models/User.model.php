<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Model_User extends Model_Main {

  # Get user name and surname
  public static final function getUserNamesAndEmail($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT name, surname, email FROM users WHERE id = :id LIMIT 1");

      $oQuery->bindParam('id', $iId);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;

      return $aResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  public static function getExistingUser($sEmail) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $oQuery = $oDb->prepare("SELECT email FROM users WHERE email = :email LIMIT 1");

      $oQuery->bindParam('email', $sEmail);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      $oDb = null;

      if (isset($aResult['email']) && !empty($aResult['email']))
        return true;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  private function _setData() {
    if (empty($this->_iId)) {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->query(" SELECT
                                    id,
                                    name,
                                    email,
                                    surname,
                                    last_login,
                                    date,
                                    use_gravatar
                                  FROM
                                    users
                                  ORDER BY
                                    id ASC");

        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($aResult as $aRow) {
          $iId = $aRow['id'];
          $aGravatar = array('use_gravatar' => $aRow['use_gravatar'], 'email' => $aRow['email']);

          # Set SEO friendly user names
          $sName      = Helper::formatOutput($aRow['name']);
          $sSurname   = Helper::formatOutput($aRow['surname']);
          $sFullName  = $sName . ' ' . $sSurname;

          $this->_aData[$iId] = array(
              'name'          => $sName,
              'surname'       => $sSurname,
              'full_name'     => $sFullName,
              'full_name_seo' => urlencode($sFullName),
              'last_login'    => Helper::formatTimestamp($aRow['last_login']),
              'date'          => Helper::formatTimestamp($aRow['date']),
              'id'            => $aRow['id'],
              'use_gravatar'  => $aRow['use_gravatar'],
              'avatar_32'     => Helper::getAvatar('user', 32, $aRow['id'], $aGravatar)
          );
        }
      } catch (AdvancedException $e) {
        $oDb->rollBack();
      }
    } else {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare("	SELECT
                                    name,
                                    surname,
                                    last_login,
                                    email,
                                    date,
                                    description,
                                    user_right,
                                    receive_newsletter,
                                    use_gravatar
                                  FROM
                                    users
                                  WHERE
                                    id = :id
                                  LIMIT 1");

        $oQuery->bindParam('id', $this->_iId);
        $oQuery->execute();

        $this->_aData = $oQuery->fetch(PDO::FETCH_ASSOC);

      } catch (AdvancedException $e) {
        $oDb->rollBack();
      }
    }
  }

  public function getData($iId = '', $bForceNoId = false) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    if($bForceNoId == true)
      $this->_iId = '';

    $this->_setData();
    return $this->_aData;
  }

  public function create($iVerificationCode) {
		try {
			$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
			$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$oQuery = $oDb->prepare(" INSERT INTO
                                  users (name, surname, password, email, date, verification_code)
                                VALUES
                                  ( :name, :surname, :password, :email, :date, :verification_code )");

			$oQuery->bindParam('name', Helper::formatInput($this->_aRequest['name']));
			$oQuery->bindParam('surname', Helper::formatInput($this->_aRequest['surname']));
			$oQuery->bindParam('password', md5(RANDOM_HASH . $this->_aRequest['password']));
			$oQuery->bindParam('email', Helper::formatInput($this->_aRequest['email']));
			$oQuery->bindParam('date', time());
			$oQuery->bindParam('verification_code', $iVerificationCode);
			$bResult = $oQuery->execute();

			$oDb = null;
			return $bResult;
		}
		catch (AdvancedException $e) {
			$oDb->rollBack();
		}
	}

  public function update($iId) {
    $iReceiveNewsletter = isset($this->_aRequest['receive_newsletter']) ? 1 : 0;
    $iUseGravatar = isset($this->_aRequest['use_gravatar']) ? 1 : 0;

    if (($iId !== USER_ID) && USER_RIGHT == 4)
      $iUserRight = isset($this->_aRequest['user_right']) && !empty($this->_aRequest['user_right']) ?
              (int) $this->_aRequest['user_right'] :
              0;
    else
      $iUserRight = USER_RIGHT;

    # Make sure the password is set and override session due to saving problems
    if (isset($this->_aRequest['password_new']) && !empty($this->_aRequest['password_new']) &&
            isset($this->_aRequest['password_old']) && !empty($this->_aRequest['password_old']))
      $this->_aSession['userdata']['password'] = md5(RANDOM_HASH . $this->_aRequest['password_new']);
    $sPassword = $this->_aSession['userdata']['password'];

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	UPDATE
                                  users
                                SET
                                  name = :name,
                                  surname = :surname,
                                  email = :email,
                                  description = :description,
                                  receive_newsletter = :receive_newsletter,
                                  use_gravatar = :use_gravatar,
                                  password = :password,
                                  user_right = :user_right
                                WHERE
                                  id = :id");

      $oQuery->bindParam('name', Helper::formatInput($this->_aRequest['name']));
      $oQuery->bindParam('surname', Helper::formatInput($this->_aRequest['surname']));
      $oQuery->bindParam('email', Helper::formatInput($this->_aRequest['email']));
      $oQuery->bindParam('description', Helper::formatInput($this->_aRequest['description']));
      $oQuery->bindParam('receive_newsletter', $iReceiveNewsletter);
      $oQuery->bindParam('use_gravatar', $iUseGravatar);
      $oQuery->bindParam('password', $sPassword);
      $oQuery->bindParam('user_right', $iUserRight);
      $oQuery->bindParam('id', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

  public function destroy($iId) {
    # Delete avatars
    @unlink(PATH_UPLOAD . '/user/32/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/64/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/100/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/200/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/popup/' . (int) $iId . '.jpg');
    @unlink(PATH_UPLOAD . '/user/original/' . (int) $iId . '.jpg');

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	DELETE FROM
                                  users
                                WHERE
                                  id = :id
                                LIMIT
                                  1");

      $oQuery->bindParam('id', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }
  }

	public function verifyEmail($iVerificationCode) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	SELECT
																	id
																FROM
																	users
																WHERE
																	verification_code = :verification_code
																LIMIT 1");

      $oQuery->bindParam('verification_code', $iVerificationCode);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    if (!empty($aResult['id'])) {
      try {
        $oQuery = $oDb->prepare("	UPDATE
																		users
																	SET
																		verification_code = ''
																	WHERE
																		id = :id");

        $oQuery->bindParam('id', $aResult['id']);
        $bResult = $oQuery->execute();

        $oDb = null;

        if ($bResult == true)
          return Model_Session::setActiveSession($aResult['id']) . Helper::redirectTo('/Start');
        else
          return false;
      }
      catch (AdvancedException $e) {
        $oDb->rollBack();
      }
    }
    else {
      $oDb = null;
      return false;
    }
  }
}
<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

class Model_User extends Model_Main {
  private function _setData() {
    if( empty($this->_iID) ) {
      $oGetData = new Query("	SELECT
																id,
																name,
                                email,
																surname,
																last_login,
																regdate,
                                use_gravatar
															FROM
																user
															ORDER BY
																id ASC");

      while( $aRow = $oGetData->fetch()) {
        $iID = $aRow['id'];
        $aGravatar = array('use_gravatar' => $aRow['use_gravatar'], 'email' => $aRow['email']);
        $this->_aData[$iID] = array(
                'name'						=> Helper::formatOutout($aRow['name']),
                'surname'					=> Helper::formatOutout($aRow['surname']),
                'last_login'			=> Helper::formatTimestamp($aRow['last_login']),
                'regdate'					=> Helper::formatTimestamp($aRow['regdate']),
                'id'							=> $aRow['id'],
                'use_gravatar'		=> $aRow['use_gravatar'],
                'avatar_32'				=> Helper::getAvatar('user', 32, $aRow['id'], $aGravatar)
        );
      }
    }
    else {
      $oGetData = new Query("	SELECT
																name,
																surname,
																last_login,
																email,
																regdate,
																description,
																userright,
																newsletter_default,
                                use_gravatar
															FROM
																user
															WHERE
																id = '"	.$this->_iID.	"'
															LIMIT 1");

      $this->_aData = $oGetData->fetch();
    }
  }

  public function getData($iID = '') {
    if( !empty($iID) )
      $this->_iID = (int)$iID;

    $this->_setData();
    return $this->_aData;
  }

  public function create() {
    return new Query("INSERT INTO
												user (name, surname, password, email, regdate)
											VALUES
											(
												'"	.Helper::formatInput($this->m_aRequest['name']).	"',
												'"	.Helper::formatInput($this->m_aRequest['surname']).	"',
												'"	.md5(RANDOM_HASH.$this->m_aRequest['password']).	"',
												'"	.Helper::formatInput($this->m_aRequest['email']).	"',
												'"	.time().	"'
											)");
  }

  public function update($iUID) {
    $iNewsletterDefault = isset($this->m_aRequest['newsletter_default']) ? 1 : 0;
    $iUseGravatar = isset($this->m_aRequest['use_gravatar']) ? 1 : 0;

    if( ($iUID !== USER_ID) && USER_RIGHT == 4 )
      $iUserRight = isset($this->m_aRequest['userright']) && !empty($this->m_aRequest['userright']) ?
              (int)$this->m_aRequest['userright'] :
              0;
    else
      $iUserRight = USER_RIGHT;

    if(isset($this->m_aRequest['newpw']) && !empty($this->m_aRequest['newpw']) &&
            isset($this->m_aRequest['newpw']) && !empty($this->m_aRequest['oldpw']))
      $this->m_oSession['userdata']['password'] = md5(RANDOM_HASH.$this->m_aRequest['newpw']);

    $sPassword = $this->m_oSession['userdata']['password'];

    return new Query("	UPDATE
													`user`
												SET
													name = '"	.Helper::formatInput($this->m_aRequest['name']).	"',
													surname = '"	.Helper::formatInput($this->m_aRequest['surname']).	"',
													email = '"	.Helper::formatInput($this->m_aRequest['email']).	"',
													description = '"	.Helper::formatInput($this->m_aRequest['description']).	"',
													newsletter_default = '"	.$iNewsletterDefault.	"',
													use_gravatar = '"	.$iUseGravatar.	"',
													password = '"	.$sPassword.	"',
													userright = '"	.$iUserRight.	"'
												WHERE
													`id` = '"	.$iUID.	"'");
  }

  public function destroy($iID) {
    # Delete avatars
    @unlink(PATH_UPLOAD.	'/user/18/'	.(int)$iID.	'.jpg');
    @unlink(PATH_UPLOAD.	'/user/32/'	.(int)$iID.	'.jpg');
    @unlink(PATH_UPLOAD.	'/user/64/'	.(int)$iID.	'.jpg');
    @unlink(PATH_UPLOAD.	'/user/100/'	.(int)$iID.	'.jpg');
    @unlink(PATH_UPLOAD.	'/user/200/'	.(int)$iID.	'.jpg');
    @unlink(PATH_UPLOAD.	'/user/original/'	.(int)$iID.	'.jpg');

    return new Query("	DELETE FROM
													`user`
												WHERE
													`id` = '"	.(int)$iID.	"'
												LIMIT 1");
  }
}
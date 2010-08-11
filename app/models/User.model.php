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
																surname,
																last_login,
																regdate
															FROM
																user
															ORDER BY
																id ASC");

			while( $aRow = $oGetData->fetch()) {
				$iID = $aRow['id'];
				$this->_aData[$iID] = array(
						'name'						=> Helper::formatBBCode($aRow['name']),
						'surname'					=> Helper::formatBBCode($aRow['surname']),
						'last_login'			=> Helper::formatTimestamp($aRow['last_login']),
						'regdate'					=> Helper::formatTimestamp($aRow['regdate']),
						'id'							=> $aRow['id'],
						'avatar_32'				=> Helper::getAvatar('user/32/', $aRow['id']),
						'avatar_original'	=> Helper::getAvatar('user/original/', $aRow['id'])
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
																newsletter_default
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
												'"	.Helper::formatHTMLCode($this->m_aRequest['name']).	"',
												'"	.Helper::formatHTMLCode($this->m_aRequest['surname']).	"',
												'"	.md5(RANDOM_HASH.$this->m_aRequest['password']).	"',
												'"	.Helper::formatHTMLCode($this->m_aRequest['email']).	"',
												'"	.time().	"'
											)");
	}

	public function update($iID) {
		$iNewsletterDefault = isset($this->m_aRequest['newsletter_default']) ? 1 : 0;

		if( ($iID !== USERID) && USERRIGHT == 4 ) {
			$iUID = $this->_iID;
			$iUserRight = isset($this->m_aRequest['userright']) && !empty($this->m_aRequest['userright']) ?
                    (int)$this->m_aRequest['userright'] :
                    0;
		}
		else {
			$iUID = USERID;
			$iUserRight = USERRIGHT;
		}

		$sPassword = isset($this->m_aRequest['newpw']) ?
				md5(RANDOM_HASH.$this->m_aRequest['newpw']) :
				$this->m_oSession['userdata']['password'];

		return new Query("	UPDATE
													`user`
												SET
													name = '"	.Helper::formatHTMLCode($this->m_aRequest['name']).	"',
													surname = '"	.Helper::formatHTMLCode($this->m_aRequest['surname']).	"',
													email = '"	.Helper::formatHTMLCode($this->m_aRequest['email']).	"',
													description = '"	.Helper::formatHTMLCode($this->m_aRequest['description']).	"',
													newsletter_default = '"	.$iNewsletterDefault.	"',
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
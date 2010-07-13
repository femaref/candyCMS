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

class Model_Content extends Model_Main {
	private final function _setData($bUpdate = false) {
		$sWhere = '';
		$sLimit = '';

		if( !empty($this->_iID) ) {
			$sWhere = "WHERE c.id = '" .$this->_iID.  "'";
			$sLimit = 'LIMIT 1';
		}

		$oGetData = new Query("	SELECT
															c.*, u.id AS uid, u.name, u.surname
														FROM
															content c
														LEFT JOIN
															user u ON c.authorID=u.id
														"	.$sWhere.	"
														ORDER BY
															c.title ASC
														"	.$sLimit
		);

		while($aRow = $oGetData->fetch()) {
			$iID = $aRow['id'];
			if( $bUpdate == true ) {
			# Do we use WYSIWYG or BB-Code?
				if( isset($this->m_aRequest['write_mode']) &&
						'wysiwyg' == $this->m_aRequest['write_mode'] )
					$sContent = Helper::formatBBCode($aRow['content']);
				else
					$sContent = Helper::removeSlahes($aRow['content']);

				$this->_aData[$iID] = array(	'id' => $aRow['id'],
						'authorID' => $aRow['authorID'],
						'title' => Helper::removeSlahes($aRow['title']),
						'content' => $sContent,
						'date' => Helper::formatTimestamp($aRow['date'])
				);
				unset($sContent);
			}
			else {
				$this->_aData[$iID] = array(	'id' => $aRow['id'],
						'authorID' => $aRow['authorID'],
						'title' => Helper::formatBBCode($aRow['title']),
						'content' => Helper::formatBBCode($aRow['content'], true),
						'date' => Helper::formatTimestamp($aRow['date']),
						'uid' => $aRow['uid'],
						'name' => Helper::formatBBCode($aRow['name']),
						'surname' => Helper::formatBBCode($aRow['surname']),
						'avatar' => '',
						'eTitle' => Helper::formatBBCode(urlencode($aRow['title']))
				);
			}
		}
	}

	public final function getData($iID = '', $bUpdate = false) {
		if( !empty($iID) )
			$this->_iID = (int)$iID;

		$this->_setData($bUpdate);
		return $this->_aData;
	}

	public function create() {
		$oQuery = new Query("	INSERT INTO
														content(authorID, title, content, date)
													VALUES(
														'"	.USERID.	"',
														'"	.Helper::formatHTMLCode($this->m_aRequest['title']).	"',
														'"	.Helper::formatHTMLCode($this->m_aRequest['content'], false).	"',
														'"	.time().	"')
														");

		#$this->_iID = mysql_insert_id();
		return $oQuery;
	}

	public function getId() {
		return $this->_iID;
	}

	public function update($iID) {
		return new Query("	UPDATE
													`content`
												SET
													title = '"	.Helper::formatHTMLCode($this->m_aRequest['title'], false).	"',
													content = '"	.Helper::formatHTMLCode($this->m_aRequest['content'], false).	"',
													date = '"	.time().	"',
													authorID = '"	.USERID.	"'
												WHERE
													`id` = '"	.(int)$iID.	"'");
	}

	public function destroy($iID) {
		return new Query("	DELETE FROM
													`content`
												WHERE
													`id` = '"	.(int)$iID.	"'
												LIMIT 1");
	}
}
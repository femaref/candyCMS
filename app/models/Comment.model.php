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

class Model_Comment extends Model_Main {
	private $_iEntries;

	public function __init($iEntries, $iOffset, $iLimit) {
		$this->_iEntries	=& $iEntries;
    $this->_iOffset   =& $iOffset;
    $this->_iLimit    =& $iLimit;
	}

	private final function _setData($parentID, $parentCat) {
		if($this->_iEntries > 0) {
			$oGetData = new Query("	SELECT
										c.*,
										u.name,
										u.surname,
										u.id AS userID
									FROM
										comment c
									LEFT JOIN
										user u
									ON
										u.id=c.authorID
									WHERE
										c.parentID = '"	.$parentID.	"'
									AND
										c.parentCat = '"	.$parentCat.	"'
									ORDER BY
										c.date ASC,
										c.id ASC
									LIMIT
										"	.$this->_iOffset.	",
										"	.$this->_iLimit );

			$iLoop = 0;
			while($aRow = $oGetData->fetch()) {
				$iLoop++;
				$iID = $aRow['id'];
				$this->_aData[$iID] =
						array(	'id' => $aRow['id'],
						'userID' => $aRow['userID'],
						'parentID' => $aRow['parentID'],
						'parentCat' => $aRow['parentCat'],
						'authorID' => $aRow['authorID'],
						'name' => Helper::formatBBCode($aRow['name']),
						'surname' => Helper::formatBBCode($aRow['surname']),
						'avatar64' => Helper::getAvatar('user/64/', $aRow['authorID']),
						'date' => Helper::formatTimestamp($aRow['date']),
						'content' => Helper::formatBBCode($aRow['content']),
						'loop' => $iLoop
				);
			}

			return $this->_aData;
		}
	}

	public final function getData($iParentID, $sParentCat) {
		return $this->_setData($iParentID, $sParentCat);
	}

	public final function countData($iParentID, $sParentCat = 'b') {
		$oQuery = new Query(" SELECT
                            COUNT(*)
                          FROM
                            comment
                          WHERE
                            parentID = '" .$iParentID.  "'
                          AND
                            parentCat = '" .$sParentCat.  "'");

    return $oQuery->count();
	}

	public function create() {
		new Query("	INSERT INTO
									comment(authorID, content, date, parentID, parentCat)
								VALUES(
									'"	.USERID.	"',
									'"	.Helper::formatHTMLCode($this->m_aRequest['content']).	"',
									'"	.time().	"',
									'"	.(int)$this->m_aRequest['parentid'].	"',
									'"	.$this->m_aRequest['parentcat'].	"')
									");

		return mysql_insert_id();
	}

	public function destroy($iID) {
		new Query("DELETE FROM comment WHERE id = '"	.$iID.	"' LIMIT 1");
		return true;
	}
}
<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
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
                'author_name' => $aRow['author_name'],
                'name' => Helper::formatOutout($aRow['name']),
                'surname' => Helper::formatOutout($aRow['surname']),
                'avatar64' => Helper::getAvatar('user/64/', $aRow['authorID']),
                'date' => Helper::formatTimestamp($aRow['date']),
                'content' => Helper::formatOutout($aRow['content']),
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
    $sAuthorName = isset($this->m_aRequest['name']) ?
            Helper::formatInput($this->m_aRequest['name']) :
            '';

    new Query("	INSERT INTO
									comment(authorID, author_name, content, date, parentID, parentCat)
								VALUES(
									'"	.USER_ID.	"',
									'"	.$sAuthorName.	"',
									'"	.Helper::formatInput($this->m_aRequest['content']).	"',
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
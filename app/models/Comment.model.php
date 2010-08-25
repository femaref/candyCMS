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
    $this->_iEntries	= & $iEntries;
    $this->_iOffset		= & $iOffset;
    $this->_iLimit		= & $iLimit;
  }

  private final function _setData($parentId, $parentCategory) {
    if ($this->_iEntries > 0) {
      try {
        $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
        $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $oQuery = $oDb->prepare("	SELECT
                                    c.*,
                                    u.name,
                                    u.surname,
                                    u.id AS userID,
                                    u.use_gravatar,
                                    u.email
                                  FROM
                                    comment c
                                  LEFT JOIN
                                    user u
                                  ON
                                    u.id=c.authorID
                                  WHERE
                                    c.parentID = :parent_id
                                  AND
                                    c.parentCat = :parent_category
                                  ORDER BY
                                    c.date ASC,
                                    c.id ASC
                                  LIMIT
                                    :offset,
                                    :limit");

        $oQuery->bindParam('parent_id', $parentId);
        $oQuery->bindParam('parent_category', $parentCategory);
        $oQuery->bindParam('offset', $this->_iOffset, PDO::PARAM_INT);
        $oQuery->bindParam('limit', $this->_iLimit, PDO::PARAM_INT);
        $oQuery->execute();

        $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);

      } catch (AdvancedException $e) {
        $oDb->rollBack();
        $e->getMessage();
      }

      $iLoop = 1;
      foreach ($aResult as $aRow) {
        $iId = $aRow['id'];

        if(isset($aRow['userID']))
          $aGravatar = array('use_gravatar' => $aRow['use_gravatar'], 'email' => $aRow['email']);
        else
          $aGravatar = array('use_gravatar' => 1, 'email' => $aRow['author_email']);

        $this->_aData[$iId] =
                array(
                    'id'            => $aRow['id'],
                    'userID'        => $aRow['userID'],
                    'parentID'      => $aRow['parentID'],
                    'parentCat'     => $aRow['parentCat'],
                    'author_id'     => $aRow['authorID'],
                    'author_email'  => $aRow['author_email'],
                    'author_name'   => $aRow['author_name'],
                    'name'          => Helper::formatOutput($aRow['name']),
                    'surname'       => Helper::formatOutput($aRow['surname']),
                    'avatar_32'     => Helper::getAvatar('user', 32, $aRow['authorID'], $aGravatar),
                    'avatar_64'     => Helper::getAvatar('user', 64, $aRow['authorID'], $aGravatar),
                    'date'          => Helper::formatTimestamp($aRow['date']),
                    'content'       => Helper::formatOutput($aRow['content']),
                    'loop'          => $iLoop
        );

        $iLoop++;
      }

      return $this->_aData;
    }
  }

  public final function getData($iParentId, $sParentCategory) {
    return $this->_setData($iParentId, $sParentCategory);
  }

  public final function countData($iParentId, $sParentCategory = 'b') {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare(" SELECT
                                  id
                                FROM
                                  comment
                                WHERE
                                  parentID = :parent_id
                                AND
                                  parentCat = :parent_category");

      $oQuery->bindParam('parent_id', $iParentId);
      $oQuery->bindParam('parent_category', $sParentCategory);

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }

    return count((int)$aResult);
  }

  public function create() {
    $sAuthorName = isset($this->_aRequest['name']) ?
            Helper::formatInput($this->_aRequest['name']) :
            '';

    $sAuthorEmail = isset($this->_aRequest['email']) ?
            Helper::formatInput($this->_aRequest['email']) :
            '';

    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare(" INSERT INTO
                                  comment(authorID, author_name, author_email, content, date, parentCat, parentID)
                                VALUES
                                  ( :author_id, :author_name, :author_email, :content, :date, :parent_category, :parent_id )");

      $iUserId = USER_ID;
      $oQuery->bindParam('author_id', $iUserId);
      $oQuery->bindParam('author_name', $sAuthorName);
      $oQuery->bindParam('author_email', $sAuthorEmail);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']));
      $oQuery->bindParam('date', time());
      $oQuery->bindParam('parent_category', $this->_aRequest['parentcat']);
      $oQuery->bindParam('parent_id', $this->_aRequest['parentid']);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;

    } catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }
  }

  public function destroy($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	DELETE FROM
                                  comment
                                WHERE
                                  id = :id
                                LIMIT
                                  1");

      $oQuery->bindParam('id', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;

    } catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }
  }
}
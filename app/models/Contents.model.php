<?php

/**
 * Handle all content SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Model;

use CandyCMS\Helper\AdvancedException as AdvancedException;
use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\Pagination as Pagination;
use PDO;

class Contents extends Main {

  /**
   * Get content entry or content overview data. Depends on available ID.
   *
   * @access public
   * @param integer $iId ID to load data from. If empty, show overview.
   * @param boolean $bUpdate prepare data for update
   * @param integer $iLimit blog post limit
   * @return array $this->_aData
   *
   */
  public function getData($iId = '', $bUpdate = false, $iLimit = 1000) {
    $iPublished = isset($this->_aSession['user']['role']) && $this->_aSession['user']['role'] >= 3 ? 0 : 1;

    if (empty($iId)) {
      try {
        $oQuery = $this->_oDb->prepare("SELECT
                                          c.*,
                                          u.id AS uid,
                                          u.name,
                                          u.surname
                                        FROM
                                          " . SQL_PREFIX . "contents c
                                        LEFT JOIN
                                          " . SQL_PREFIX . "users u
                                        ON
                                          c.author_id=u.id
                                        WHERE
                                          published >= :published
                                        ORDER BY
                                          c.title ASC
                                        LIMIT :limit");

        $oQuery->bindParam('published', $iPublished, PDO::PARAM_INT);
        $oQuery->bindParam('limit', $iLimit, PDO::PARAM_INT);
        $oQuery->execute();

        $aResult = & $oQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      catch (\PDOException $p) {
        AdvancedException::reportBoth('0024 - ' . $p->getMessage());
        exit('SQL error.');
      }
    }
    else {
      try {
        $oQuery = $this->_oDb->prepare("SELECT
                                          c.*,
                                          u.id AS uid,
                                          u.name,
                                          u.surname
                                        FROM
                                          " . SQL_PREFIX . "contents c
                                        LEFT JOIN
                                          " . SQL_PREFIX . "users u
                                        ON
                                          c.author_id=u.id
                                        WHERE
                                          c.id = :id
                                        AND
                                          published >= :published
                                        LIMIT
                                          1");

        $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
        $oQuery->bindParam('published', $iPublished, PDO::PARAM_INT);
        $oQuery->execute();

        # Bugfix: Give array to template to enable a loop.
        $aResult = & $oQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      catch (\PDOException $p) {
        AdvancedException::reportBoth('0025 - ' . $p->getMessage());
        exit('SQL error.');
      }
    }

    $aInts = array('id', 'uid', 'author_id');
    $aBools = array('published');
    foreach ($aResult as $aRow) {
      if ($bUpdate === true)
        $this->_aData = $this->_formatForUpdate($aRow);

      else {
        $iId = $aRow['id'];
        $this->_aData[$iId] = $this->_formatForOutput($aRow, $this->_aRequest['controller'], $aInts, $aBools);
      }
    }

    return $this->_aData;
  }

  /**
   * Create a content entry.
   *
   * @access public
   * @return boolean status of query
   *
   */
  public function create() {
    $this->_aRequest['published'] = isset($this->_aRequest['published']) ?
            (int) $this->_aRequest['published'] :
            0;

    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
                                        " . SQL_PREFIX . "contents
                                        ( author_id,
                                          title,
                                          teaser,
                                          keywords,
                                          content,
                                          date,
                                          published)
                                      VALUES
                                        ( :author_id,
                                          :title,
                                          :teaser,
                                          :keywords,
                                          :content,
                                          :date,
                                          :published)");

      $oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false), PDO::PARAM_STR);
      $oQuery->bindParam('teaser', Helper::formatInput($this->_aRequest['teaser']), PDO::PARAM_STR);
      $oQuery->bindParam('keywords', Helper::formatInput($this->_aRequest['keywords']), PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false), PDO::PARAM_STR);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);
      $oQuery->bindParam('published', $this->_aRequest['published'], PDO::PARAM_INT);

      $bReturn = $oQuery->execute();
      parent::$iLastInsertId = Helper::getLastEntry('contents');

      return $bReturn;
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0026 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0027 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Update a content entry.
   *
   * @access public
   * @param integer $iId ID to update
   * @return boolean status of query
   *
   */
  public function update($iId) {
    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "contents
                                      SET
                                        title = :title,
                                        teaser = :teaser,
                                        keywords = :keywords,
                                        content = :content,
                                        date = :date,
                                        author_id = :author_id,
                                        published = :published
                                      WHERE
                                        id = :where");

      $oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false), PDO::PARAM_STR);
      $oQuery->bindParam('teaser', Helper::formatInput($this->_aRequest['teaser']), PDO::PARAM_STR);
      $oQuery->bindParam('keywords', Helper::formatInput($this->_aRequest['keywords']), PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false), PDO::PARAM_STR);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);
      $oQuery->bindParam('published', $this->_aRequest['published'], PDO::PARAM_INT);
      $oQuery->bindParam('where', $iId, PDO::PARAM_INT);

      return $oQuery->execute();
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0028 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0029 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Delete a content entry.
   *
   * @access public
   * @param integer $iId ID to delete
   * @return boolean status of query
   *
   */
  public function destroy($iId) {
    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
                                        " . SQL_PREFIX . "contents
                                      WHERE
                                        id = :id
                                      LIMIT
                                        1");

      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);

      return $oQuery->execute();
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0030 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0031 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }
}
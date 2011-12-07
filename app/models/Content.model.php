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
use CandyCMS\Helper\Page as Page;
use PDO;

class Content extends Main {

  /**
   * Set content entry or content overview data.
   *
   * @access private
   * @param boolean $bUpdate prepare data for update
   * @param integer $iLimit content post limit
   * @return array data
   *
   */
  private final function _setData($bUpdate, $iLimit) {

    # Show overview
    if (empty($this->_iId)) {
      try {
        $oQuery = $this->_oDb->query("SELECT
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
                                      ORDER BY
                                        c.title ASC
                                      LIMIT " . $iLimit);

        $aResult = & $oQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

    # Show ID
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
                                        LIMIT
                                          1");

        $oQuery->bindParam('id', $this->_iId, PDO::PARAM_INT);
        $oQuery->execute();

        # Fix for using it in the same template as overview
        $aResult = & $oQuery->fetchAll(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }
    }

    foreach ($aResult as $aRow) {
      if ($bUpdate == true)
        $this->_aData = $this->_formatForUpdate($aRow);

      else {
        $iId = $aRow['id'];
        $this->_aData[$iId] = $this->_formatForOutput($aRow, 'content');
      }
    }

    return $this->_aData;
  }

  /**
   * Get content entry or content overview data. Depends on avaiable ID.
   *
   * @access public
   * @param integer $iId ID to load data from. If empty, show overview.
   * @param boolean $bUpdate prepare data for update
   * @param integer $iLimit blog post limit
   * @return array data from _setData
   *
   */
  public final function getData($iId = '', $bUpdate = false, $iLimit = 1000) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    return $this->_setData($bUpdate, $iLimit);
  }

  /**
   * Create a content entry.
   *
   * @access public
   * @return boolean status of query
   * @override app/models/Main.model.php
   *
   */
  public function create() {
    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
                                        " . SQL_PREFIX . "contents
                                        ( author_id,
                                          title,
                                          teaser,
                                          keywords,
                                          content,
                                          date)
                                      VALUES
                                        ( :author_id,
                                          :title,
                                          :teaser,
                                          :keywords,
                                          :content,
                                          :date )");

      $iUserId = USER_ID;
      $oQuery->bindParam('author_id', $iUserId, PDO::PARAM_INT);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false), PDO::PARAM_STR);
      $oQuery->bindParam('teaser', Helper::formatInput($this->_aRequest['teaser']), PDO::PARAM_STR);
      $oQuery->bindParam('keywords', Helper::formatInput($this->_aRequest['keywords']), PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false), PDO::PARAM_STR);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  /**
   * Update a content entry.
   *
   * @access public
   * @param integer $iId ID to update
   * @return boolean status of query
   * @override app/models/Main.model.php
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
                                        author_id = :user_id
                                      WHERE
                                        id = :where");

      $iUserId = USER_ID;
      $oQuery->bindParam('user_id', $iUserId, PDO::PARAM_INT);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false), PDO::PARAM_STR);
      $oQuery->bindParam('teaser', Helper::formatInput($this->_aRequest['teaser']), PDO::PARAM_STR);
      $oQuery->bindParam('keywords', Helper::formatInput($this->_aRequest['keywords']), PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::removeSlahes($this->_aRequest['content'], false), PDO::PARAM_STR);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);
      $oQuery->bindParam('where', $iId, PDO::PARAM_INT);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }

  /**
   * Delete a content entry.
   *
   * @access public
   * @param integer $iId ID to delete
   * @return boolean status of query
   * @override app/models/Main.model.php
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
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }
}
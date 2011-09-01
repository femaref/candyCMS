<?php

/**
 * Handle all blog SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

if(!class_exists('Pages'))
  require_once 'app/helpers/Page.helper.php';

class Model_Blog extends Model_Main {

	/**
	 * Set blog entry or blog overview data.
	 *
	 * @access private
	 * @param boolean $bUpdate prepare data for update
	 * @param integer $iLimit blog post limit
	 * @return array data
	 *
	 */
	private function _setData($bUpdate, $iLimit) {

    # Show overview
		if (empty($this->_iId)) {

			# Show unpublished items to moderators or administrators only
			$sWhere = USER_RIGHT < 3 ? "WHERE published = '1'" : '';

			# Search blog for tags
			if (isset($this->_aRequest['action']) && 'search' == $this->_aRequest['action'] &&
							isset($this->_aRequest['id']) && !empty($this->_aRequest['id'])) {

				$sWhere .= isset($sWhere) && !empty($sWhere) ? ' AND ' : ' WHERE ';
				$sWhere .= "tags LIKE '%" . Helper::formatInput($this->_aRequest['id']) . "%'";
			}

      # Count entries for pagination
			try {
        $oQuery = $this->_oDb->query("SELECT COUNT(*) FROM " . SQL_PREFIX . "blogs " . $sWhere);
        $iResult = $oQuery->fetchColumn();
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

			$this->oPage = new Page($this->_aRequest, (int)$iResult, $iLimit);

			try {
				$oQuery = $this->_oDb->query("SELECT
																				b.*,
																				u.id AS uid,
																				u.name,
																				u.surname,
																				u.email,
																				u.use_gravatar,
																				COUNT(c.id) AS comment_sum
																			FROM
																				" . SQL_PREFIX . "blogs b
																			LEFT JOIN
																				" . SQL_PREFIX . "users u
																			ON
																				b.author_id=u.id
																			LEFT JOIN
																				" . SQL_PREFIX . "comments c
																			ON
																				c.parent_id=b.id
																				" . $sWhere . "
																			GROUP BY
																				b.id
																			ORDER BY
																				b.date DESC
																			LIMIT
																				" . $this->oPage->getOffset() . ",
																				" . $this->oPage->getLimit());

				$aResult = & $oQuery->fetchAll(PDO::FETCH_ASSOC);
			}
			catch (AdvancedException $e) {
				$this->_oDb->rollBack();
			}

			foreach ($aResult as $aRow) {
				$iId = $aRow['id'];

				$this->_aData[$iId] = $this->_formatForOutput($aRow, 'blog');
				$this->_aData[$iId]['tags'] = explode(', ', $aRow['tags']);
				$this->_aData[$iId]['tags_raw'] = $aRow['tags'];
				$this->_aData[$iId]['date_modified'] = !empty($aRow['date_modified']) ?
								Helper::formatTimestamp($aRow['date_modified']) :
								'';
			}
		}
		# Show ID
		else {

			# Show unpublished items to moderators or administrators only
			$sWhere = USER_RIGHT < 3 ? "AND published = '1'" : '';

      try {
        $oQuery = $this->_oDb->query("SELECT
                                        b.*,
                                        u.id AS uid,
                                        u.name,
                                        u.surname,
																				u.email,
																				u.use_gravatar,
                                        COUNT(c.id) AS comment_sum
                                      FROM
                                        " . SQL_PREFIX . "blogs b
                                      LEFT JOIN
                                        " . SQL_PREFIX . "users u
                                      ON
                                        b.author_id=u.id
                                      LEFT JOIN
                                        " . SQL_PREFIX . "comments c
                                      ON
                                        c.parent_id=b.id
                                      WHERE
                                        b.id = '" . Helper::formatInput($this->_iId) . "'
                                      " . $sWhere . "
                                      LIMIT 1");

        $aRow = & $oQuery->fetch(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      # Edit entry
			if ($bUpdate == true)
				$this->_aData = $this->_formatForUpdate($aRow);

			# Blog entry
			else {
				$this->_aData[1] = $this->_formatForOutput($aRow, 'blog');
				$this->_aData[1]['tags'] = explode(', ', $aRow['tags']);
				$this->_aData[1]['tags_raw'] = $aRow['tags'];
				$this->_aData[1]['date_modified'] = !empty($aRow['date_modified']) ?
								Helper::formatTimestamp($aRow['date_modified']) :
								'';
			}
    }

    return $this->_aData;
	}

	/**
	 * Get blog entry or blog overview data. Depends on avaiable ID.
	 *
	 * @access public
	 * @param integer $iId ID to load data from. If empty, show overview.
	 * @param boolean $bUpdate prepare data for update
	 * @param integer $iLimit blog post limit
	 * @return array data from _setData
	 *
	 */
	public final function getData($iId = '', $bUpdate = false, $iLimit = LIMIT_BLOG) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    return $this->_setData($bUpdate, $iLimit);
  }

	/**
	 * Create a blog entry.
	 *
	 * @access public
	 * @return boolean status of query
	 * @override app/models/Main.model.php
	 *
	 */
	public function create() {
    $this->_aRequest['published'] = isset($this->_aRequest['published']) ?
            (int) $this->_aRequest['published'] :
            0;

    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
																				" . SQL_PREFIX . "blogs
																				( author_id,
                                          title,
                                          tags,
                                          teaser,
                                          keywords,
                                          content,
                                          date,
                                          published)
																			VALUES
																				( :author_id,
                                          :title,
                                          :tags,
                                          :teaser,
                                          :keywords,
                                          :content,
                                          :date,
                                          :published )");

      $iUserId = USER_ID;
      $oQuery->bindParam('author_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('tags', Helper::formatInput($this->_aRequest['tags']));
      $oQuery->bindParam('teaser', Helper::formatInput($this->_aRequest['teaser'], false));
      $oQuery->bindParam('keywords', Helper::formatInput($this->_aRequest['keywords']));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false));
      $oQuery->bindParam('date', time());
      $oQuery->bindParam('published', $this->_aRequest['published']);

			return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
	}

	/**
	 * Update a blog entry.
	 *
	 * @access public
	 * @param integer $iId ID to update
	 * @return boolean status of query
	 * @override app/models/Main.model.php
	 *
	 */
	public function update($iId) {
		$iDateModified = (isset($this->_aRequest['show_update']) && $this->_aRequest['show_update'] == true) ?
            time() :
            '';

    $iPublished = (isset($this->_aRequest['published']) && $this->_aRequest['published'] == true) ?
            '1' :
            '0';

    $iUpdateAuthor = (isset($this->_aRequest['show_update']) && $this->_aRequest['show_update'] == true) ?
            USER_ID :
            (int) $this->_aRequest['author_id'];

    try {
      $oQuery = $this->_oDb->prepare("UPDATE
                                        " . SQL_PREFIX . "blogs
                                      SET
                                        author_id = :author_id,
                                        title = :title,
                                        tags = :tags,
                                        teaser = :teaser,
                                        keywords = :keywords,
                                        content = :content,
                                        date_modified = :date_modified,
                                        published = :published
                                      WHERE
                                        id = :id");

      $oQuery->bindParam('author_id', $iUpdateAuthor);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('tags', Helper::formatInput($this->_aRequest['tags']));
      $oQuery->bindParam('teaser', Helper::formatInput($this->_aRequest['teaser'], false));
      $oQuery->bindParam('keywords', Helper::formatInput($this->_aRequest['keywords']));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false));
      $oQuery->bindParam('date_modified', $iDateModified);
      $oQuery->bindParam('published', $iPublished);
      $oQuery->bindParam('id', $iId);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
	}

	/**
	 * Delete a blog entry and also delete its comments.
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
																				" . SQL_PREFIX . "blogs
																			WHERE
																				id = :id
																			LIMIT
																				1");

      $oQuery->bindParam('id', $iId);
      $bResult = $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
																				" . SQL_PREFIX . "comments
																			WHERE
																				parent_id = :parent_id");

      $oQuery->bindParam('parent_id', $iId);

      $bResult = $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }

		return $bResult;
  }
}
<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

if(!class_exists('Pages'))
  require_once 'app/helpers/Page.helper.php';

class Model_Blog extends Model_Main {

	private function _setData($bEdit = false) {
		$sWhere	= '';

		if (empty($this->_iId)) {
			if (USER_RIGHT < 3)
				$sWhere = "WHERE published = '1'";

			# Search Blog for Tags
			if (isset($this->_aRequest['action']) && 'search' == $this->_aRequest['action'] &&
              isset($this->_aRequest['id']) && !empty($this->_aRequest['id'])) {
        if (empty($sWhere))
          $sWhere = "WHERE tags LIKE '%" .
                  Helper::formatInput($this->_aRequest['id']) . "%'";
        else
          $sWhere .= "AND tags LIKE '%" .
                  Helper::formatInput($this->_aRequest['id']) . "%'";
      }

			try {
        $oQuery = $this->_oDb->query("SELECT COUNT(*) FROM " . SQL_PREFIX . "blogs " . $sWhere);
        $iResult = $oQuery->fetchColumn();
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

			$this->oPage = new Page($this->_aRequest, (int)$iResult, LIMIT_BLOG);
 
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
				$aTags = explode(', ', $aRow['tags']);
				$aGravatar = array('use_gravatar' => $aRow['use_gravatar'], 'email' => $aRow['email']);
        $sEncodedTitle = Helper::formatOutput(urlencode($aRow['title']));
        $sUrl = WEBSITE_URL . '/blog/' . $iId;

        # Set SEO friendly user names
        $sName      = Helper::formatOutput($aRow['name']);
        $sSurname   = Helper::formatOutput($aRow['surname']);
        $sFullName  = $sName . ' ' . $sSurname;

        $this->_aData[$iId] = array(
                'id'                => $aRow['id'],
                'uid'               => $aRow['uid'],
                'author_id'         => $aRow['author_id'],
                'tags'              => $aTags,
                'tags_raw'          => $aRow['tags'],
                'title'             => Helper::formatOutput($aRow['title']),
                'teaser'            => Helper::formatOutput($aRow['teaser']),
                'keywords'          => Helper::formatOutput($aRow['keywords']),
                'content'           => Helper::formatOutput($aRow['content']),
                'date'              => Helper::formatTimestamp($aRow['date'], true),
                'datetime'          => Helper::formatTimestamp($aRow['date']),
                'date_raw'          => $aRow['date'],
                'date_rss'          => date('r', $aRow['date']),
                'date_w3c'          => date(DATE_W3C),
                'encoded_full_name' => urlencode($sFullName),
                'encoded_title'     => $sEncodedTitle, # encoded for social networks
                'encoded_url'       => urlencode($sUrl),
                'avatar_64'         => Helper::getAvatar('user', 64, $aRow['author_id'], $aGravatar),
                'full_name'         => $sFullName,
                'name'              => $sName,
                'surname'           => $sSurname,
                'comment_sum'       => $aRow['comment_sum'],
                'published'         => $aRow['published'],
                'url'               => $sUrl . '/' . $sEncodedTitle,
                'url_clean'         => $sUrl
				);

				if (!empty($aRow['date_modified']))
					$this->_aData[$iId]['date_modified'] = Helper::formatTimestamp($aRow['date_modified']);

				else
					$this->_aData[$iId]['date_modified'] = '';
			}
		}
		# There's an ID so choose between editing or displaying entry
		else {
			if (USER_RIGHT < 3)
				$sWhere = "AND b.published = '1'";

      try {
        $oQuery = $this->_oDb->query("SELECT
                                        b.*,
                                        u.id AS uid,
                                        u.name,
                                        u.surname,
                                        COUNT(c.id) AS commentSum
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
                                      GROUP BY
                                        b.title
                                      LIMIT 1");

        $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      }
      catch (AdvancedException $e) {
        $this->_oDb->rollBack();
      }

      $aRow =& $aResult;

      # Edit only
      if ($bEdit == true) {
        $this->_aData = array(
            'id'        => $aRow['id'],
            'author_id'	=> $aRow['author_id'],
            'tags'      => Helper::removeSlahes($aRow['tags']),
            'title'     => Helper::removeSlahes($aRow['title']),
            'teaser'    => Helper::removeSlahes($aRow['teaser']),
            'keywords'  => Helper::removeSlahes($aRow['keywords']),
            'content'   => Helper::removeSlahes($aRow['content']),
            'date'      => Helper::formatTimestamp($aRow['date'], true),
            'datetime'  => Helper::formatTimestamp($aRow['date']),
            'published' => $aRow['published']
        );
        unset($sContent);
      }
      # Give back blog entry
      else {
        $aTags = explode(', ', $aRow['tags']);
        $sEncodedTitle = Helper::formatOutput(urlencode($aRow['title']));
        $sUrl = WEBSITE_URL . '/blog/' . $aRow['id'];

        # Set SEO friendly user names
        $sName      = Helper::formatOutput($aRow['name']);
        $sSurname   = Helper::formatOutput($aRow['surname']);
        $sFullName  = $sName . ' ' . $sSurname;

        # Do we need to highlight text?
        $sHighlight = isset($this->_aRequest['highlight']) && !empty($this->_aRequest['highlight']) ?
                $this->_aRequest['highlight'] :
                '';

        $this->_aData[1] = array(
            'id'                => $aRow['id'],
            'uid'               => $aRow['uid'],
            'author_id'         => $aRow['author_id'],
            'tags'              => $aTags,
            'tags_raw'          => $aRow['tags'],
            'title'             => Helper::formatOutput($aRow['title'], $sHighlight),
            'teaser'            => Helper::formatOutput($aRow['teaser'], $sHighlight),
            'keywords'          => Helper::formatOutput($aRow['keywords']),
            'content'           => Helper::formatOutput($aRow['content'], $sHighlight),
            'date'              => Helper::formatTimestamp($aRow['date']),
            'datetime'          => Helper::formatTimestamp($aRow['date']),
            'date_raw'          => $aRow['date'],
            'date_w3c'          => date(DATE_W3C),
            'name'              => $sName,
            'surname'           => $sSurname,
            'full_name'         => $sFullName,
            'encoded_full_name' => urlencode($sFullName),
            'encoded_title'     => $sEncodedTitle,
            'encoded_url'       => urlencode($sUrl),
            'avatar'            => '',
            'comment_sum'       => $aRow['commentSum'],
            'published'         => $aRow['published'],
            'url'               => $sUrl . '#' . $sEncodedTitle,
            'url_clean'         => $sUrl
        );

        if (!empty($aRow['date_modified']))
          $this->_aData[1]['date_modified'] = Helper::formatTimestamp($aRow['date_modified']);

        else
          $this->_aData[1]['date_modified'] = '';
      }
    }
	}

	public final function getData($iId = '', $bEdit = false) {
    if (!empty($iId))
      $this->_iId = (int) $iId;

    $this->_setData($bEdit);
    return $this->_aData;
  }

	public function create() {
    $this->_aRequest['published'] = isset($this->_aRequest['published']) ?
            (int) $this->_aRequest['published'] :
            0;

    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
																				" . SQL_PREFIX . "blogs
																				(author_id, title, tags, teaser, keywords, content, date, published)
																			VALUES
																				( :author_id, :title, :tags, :teaser, :keywords, :content, :date, :published )");

      $iUserId = USER_ID;
      $oQuery->bindParam('author_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('tags', Helper::formatInput($this->_aRequest['tags']));
      $oQuery->bindParam('teaser', Helper::formatInput($this->_aRequest['teaser'], false));
      $oQuery->bindParam('keywords', Helper::formatInput($this->_aRequest['keywords']));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false));
      $oQuery->bindParam('date', time());
      $oQuery->bindParam('published', $this->_aRequest['published']);

			$bResult = $oQuery->execute();
      return $bResult;
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
	}

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
      $oQuery = $this->_oDb->prepare("	UPDATE
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

      $bResult = $oQuery->execute();
      return $bResult;
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
	}

	public final function destroy($iId) {
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
																				parent_id = :parent_id
																			AND
																				parent_category = :parent_category");

      $sParentCategory = 'b';
      $oQuery->bindParam('parent_category', $sParentCategory);
      $oQuery->bindParam('parent_id', $iId);

      $bResult = $oQuery->execute();
      return $bResult;
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }
}
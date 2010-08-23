<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Model_Blog extends Model_Main {

	private function _setData($bEdit = false) {
		$iLimit = LIMIT_BLOG;
		$sWhere	= '';

		if (empty($this->_iID)) {
			if (USER_RIGHT < 3)
				$sWhere = "WHERE published = '1'";

			# Search Blog for Tags
			if (isset($this->_aRequest['action']) && 'tag' == $this->_aRequest['action'] &&
							isset($this->_aRequest['id']) && !empty($this->_aRequest['id'])) {
				if (empty($sWhere))
					$sWhere = "WHERE tags LIKE '%" .
									Helper::formatInput($this->_aRequest['id']) . "%'";
				else
					$sWhere .= "AND tags LIKE '%" .
									Helper::formatInput($this->_aRequest['id']) . "%'";
			}

			try {
				$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
										PDO::ATTR_PERSISTENT => true
								));
				$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$oQuery = $oDb->query("SELECT id FROM blog " . $sWhere);
				$aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
			}
			catch (AdvancedException $e) {
				$oDb->rollBack();
				$e->getMessage();
			}

			$this->oPages = new Pages($this->_aRequest, count((int)$aResult), $iLimit);

			try {
				$oQuery = $oDb->query("	SELECT
																b.*,
																u.id AS uid,
																u.name,
																u.surname,
																u.email,
																u.use_gravatar,
																COUNT(c.id) AS commentSum
															FROM
																blog b
															LEFT JOIN
																user u
															ON
																b.authorID=u.id
															LEFT JOIN
																comment c
															ON
																c.parentID=b.id AND c.parentCat='b'
															" . $sWhere . "
															GROUP BY
																b.id
															ORDER BY
																b.date DESC
															LIMIT
																" . $this->oPages->getOffset() . ",
																" . $this->oPages->getLimit());

				$aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
				$oDb = null;
			}
			catch (AdvancedException $e) {
				$oDb->rollBack();
				$e->getMessage();
			}

			foreach ($aResult as $aRow) {
				$iId = $aRow['id'];
				$aTags = explode(', ', $aRow['tags']);
				$aGravatar = array('use_gravatar' => $aRow['use_gravatar'], 'email' => $aRow['email']);

        $this->_aData[$iId] = array(
                'id'          => $aRow['id'],
                'author_id'   => $aRow['authorID'],
                'tags'        => $aTags,
                'tags_sum'    => (int)count($aTags),
                'title'       => Helper::formatOutput($aRow['title']),
                'content'     => Helper::formatOutput($aRow['content'], true),
                'date'        => Helper::formatTimestamp($aRow['date']),
                'uid'         => $aRow['uid'],
                'name'        => Helper::formatOutput($aRow['name']),
                'surname'     => Helper::formatOutput($aRow['surname']),
                'avatar_32'			=> Helper::getAvatar('user', 32, $aRow['authorID'], $aGravatar),
                'avatar_64'			=> Helper::getAvatar('user', 64, $aRow['authorID'], $aGravatar),
                'comment_sum'	=> $aRow['commentSum'],
                'eTitle'      => Helper::formatOutput(urlencode($aRow['title'])),
                'published'			=> $aRow['published']
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

			$this->oPages = new Pages($this->_aRequest, 1);

			try {
				$oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
				$oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$oQuery = $oDb->query("	SELECT
																	b.*,
																	u.id AS uid,
																	u.name,
																	u.surname,
																	COUNT(c.id) AS commentSum
																FROM
																	blog b
																LEFT JOIN
																	user u
																ON
																	b.authorID=u.id
																LEFT JOIN
																	comment c
																ON
																	c.parentID=b.id AND c.parentCat='b'
																WHERE
																	b.id = '" . Helper::formatInput($this->_iID) . "'
																" . $sWhere . "
																GROUP BY
																	b.title
																LIMIT 1");

				$aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
				$oDb = null;
			}
			catch (AdvancedException $e) {
				$oDb->rollBack();
				$e->getMessage();
			}

			$aRow =& $aResult;

			# Edit only
			if ($bEdit == true) {
				$this->_aData = array(
						'id'        => $aRow['id'],
						'author_id'	=> $aRow['authorID'],
						'tags'      => Helper::removeSlahes($aRow['tags']),
						'title'     => Helper::removeSlahes($aRow['title']),
						'content'   => Helper::removeSlahes($aRow['content']),
						'date'      => Helper::formatTimestamp($aRow['date']),
						'published' => $aRow['published']
				);
				unset($sContent);
			}
			# Give back blog entry
			else {
				$aTags = explode(', ', $aRow['tags']);
				$this->_aData[1] = array(
						'id'          => $aRow['id'],
						'author_id'   => $aRow['authorID'],
						'tags'        => $aTags,
						'tags_sum'    => (int) count($aTags),
						'title'       => Helper::formatOutput($aRow['title']),
						'content'     => Helper::formatOutput($aRow['content'], true),
						'date'        => Helper::formatTimestamp($aRow['date']),
						'uid'         => $aRow['uid'],
						'name'        => Helper::formatOutput($aRow['name']),
						'surname'     => Helper::formatOutput($aRow['surname']),
						'avatar'      => '',
						'comment_sum'	=> $aRow['commentSum'],
						'eTitle'      => Helper::formatOutput(urlencode($aRow['title'])),
						'published'			=> $aRow['published']
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
			$this->_iID = (int) $iId;

		$this->_setData($bEdit);
		return $this->_aData;
	}

	public function create() {
		$this->_aRequest['published'] = isset($this->_aRequest['published']) ?
						(int)$this->_aRequest['published'] :
						0;
		
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare(" INSERT INTO
                                  blog(authorID, title, tags, content, date, published)
                                VALUES
                                  ( :user_id, :title, :tags, :content, :date, :published )");

      $iUserId = USER_ID;
      $oQuery->bindParam('user_id', $iUserId);
      $oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
      $oQuery->bindParam('tags', Helper::formatInput($this->_aRequest['tags']));
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false));
      $oQuery->bindParam('date', time());
      $oQuery->bindParam('published', $this->_aRequest['published']);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;

    } catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
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
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD);
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	UPDATE
                                  blog
                                SET
                                  authorID = :author_id,
                                  title = :title,
                                  tags = :tags,
                                  content = :content,
                                  date_modified = :date_modified,
																	published = :published
                                WHERE
                                  id = :id");

			$oQuery->bindParam('author_id', $iUpdateAuthor);
			$oQuery->bindParam('title', Helper::formatInput($this->_aRequest['title'], false));
			$oQuery->bindParam('tags', Helper::formatInput($this->_aRequest['tags']));
			$oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content'], false));
			$oQuery->bindParam('date_modified', $iDateModified);
			$oQuery->bindParam('published', $iPublished);
			$oQuery->bindParam('id', $iId);
			$bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }
	}

	public final function destroy($iId) {
    try {
      $oDb = new PDO('mysql:host=' . SQL_HOST . ';dbname=' . SQL_DB, SQL_USER, SQL_PASSWORD, array(
										PDO::ATTR_PERSISTENT => true
								));
      $oDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $oQuery = $oDb->prepare("	DELETE FROM
                                  blog
                                WHERE
                                  id = :id
                                LIMIT
                                  1");

      $oQuery->bindParam('id', $iId);
      $bResult = $oQuery->execute();

    } catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }

    try {
      $oQuery = $oDb->prepare("	DELETE FROM
                                  comment
                                WHERE
                                  parentID = :parent_id
																AND
																	parentCat = :parent_cat");

			$sParentCat = 'b';
      $oQuery->bindParam('parent_cat', $sParentCat);
      $oQuery->bindParam('parent_id', $iId);
      $bResult = $oQuery->execute();

      $oDb = null;
      return $bResult;

    } catch (AdvancedException $e) {
      $oDb->rollBack();
      $e->getMessage();
    }
	}
}
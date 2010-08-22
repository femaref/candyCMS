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
			if (isset($this->m_aRequest['action']) && 'tag' == $this->m_aRequest['action'] &&
							isset($this->m_aRequest['id']) && !empty($this->m_aRequest['id'])) {
				if (empty($sWhere))
					$sWhere = "WHERE tags LIKE '%" .
									Helper::formatInput($this->m_aRequest['id']) . "%'";
				else
					$sWhere .= "AND tags LIKE '%" .
									Helper::formatInput($this->m_aRequest['id']) . "%'";
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
				die();
			}

			$this->oPages = new Pages($this->m_aRequest, count((int)$aResult), $iLimit);

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
				die();
			}

			foreach ($aResult as $aRow) {
				$iId = $aRow['id'];
				$aTags = explode(', ', $aRow['tags']);
				$aGravatar = array('use_gravatar' => $aRow['use_gravatar'], 'email' => $aRow['email']);

        $this->_aData[$iId] = array(
								'id'						=> $aRow['id'],
                'authorID'			=> $aRow['authorID'],
                'tags'					=> $aTags,
                'tags_sum'			=> (int)count($aTags),
                'title'					=> Helper::formatOutput($aRow['title']),
                'content'				=> Helper::formatOutput($aRow['content'], true),
                'date'					=> Helper::formatTimestamp($aRow['date']),
                'uid'						=> $aRow['uid'],
                'name'					=> Helper::formatOutput($aRow['name']),
                'surname'				=> Helper::formatOutput($aRow['surname']),
                'avatar_32'			=> Helper::getAvatar('user', 32, $aRow['authorID'], $aGravatar),
                'avatar_64'			=> Helper::getAvatar('user', 64, $aRow['authorID'], $aGravatar),
                'comment_sum'		=> $aRow['commentSum'],
                'eTitle'				=> Helper::formatOutput(urlencode($aRow['title'])),
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

			$this->oPages = new Pages($this->m_aRequest, 1);

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
				die();
			}

			$aRow =& $aResult;

			# Edit only
			if ($bEdit == true) {
				$this->_aData = array(
						'id'				=> $aRow['id'],
						'authorID'	=> $aRow['authorID'],
						'tags'			=> Helper::removeSlahes($aRow['tags']),
						'title'			=> Helper::removeSlahes($aRow['title']),
						'content'		=> Helper::formatOutput($aRow['content']),
						'date'			=> Helper::formatTimestamp($aRow['date']),
						'published' => $aRow['published']
				);
				unset($sContent);
			}
			# Give back blog entry
			else {
				$aTags = explode(', ', $aRow['tags']);
				$this->_aData[1] = array(
						'id'						=> $aRow['id'],
						'authorID'			=> $aRow['authorID'],
						'tags'					=> $aTags,
						'tags_sum'			=> (int) count($aTags),
						'title'					=> Helper::formatOutput($aRow['title']),
						'content'				=> Helper::formatOutput($aRow['content'], true),
						'date'					=> Helper::formatTimestamp($aRow['date']),
						'uid'						=> $aRow['uid'],
						'name'					=> Helper::formatOutput($aRow['name']),
						'surname'				=> Helper::formatOutput($aRow['surname']),
						'avatar'				=> '',
						'comment_sum'		=> $aRow['commentSum'],
						'eTitle'				=> Helper::formatOutput(urlencode($aRow['title'])),
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
		$this->m_aRequest['published'] = isset($this->m_aRequest['published']) ?
						$this->m_aRequest['published'] :
						0;

		return new Query("INSERT INTO
												blog(authorID, title, tags, content, published, date)
											VALUES(
												'" . USER_ID . "',
												'" . Helper::formatInput($this->m_aRequest['title'], false) . "',
												'" . Helper::formatInput($this->m_aRequest['tags']) . "',
												'" . Helper::formatInput($this->m_aRequest['content'], false) . "',
												'" . (int) $this->m_aRequest['published'] . "',
												'" . time() . "')
												");
	}

	public function update($iId) {
		$iDateModified = (isset($this->m_aRequest['show_update']) && $this->m_aRequest['show_update'] == true) ?
						time() :
						'';

		$iPublished = (isset($this->m_aRequest['published']) && $this->m_aRequest['published'] == true) ?
						'1' :
						'0';

		$sUpdateAuthor = (isset($this->m_aRequest['show_update']) && $this->m_aRequest['show_update'] == true) ?
						", authorID = '" . USER_ID . "'" :
						'';

		return new Query("UPDATE
												`blog`
											SET
												title = '" . Helper::formatInput($this->m_aRequest['title'], false) . "',
												tags = '" . Helper::formatInput($this->m_aRequest['tags']) . "',
												content = '" . Helper::formatInput($this->m_aRequest['content'], false) . "',
												published = '" . (int) $iPublished . "',
												date_modified = '" . $iDateModified . "'
												" . $sUpdateAuthor . "
											WHERE
												`id` = '" . $iId . "'");
	}

	public final function destroy($iId) {
		new Query("DELETE FROM blog WHERE id = '" . $iId . "' LIMIT 1");
		new Query("DELETE FROM comment WHERE parentID = '" . $iId . "' AND parentCat = 'b'");
		return true;
	}
}
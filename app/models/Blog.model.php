<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

class Model_Blog extends Model_Main {
  private function _setData($bEdit = false) {
    $iLimit	= LIMIT_BLOG;
    $sWhere = '';

    # Get all the entries
    if ( empty($this->_iID)) {
      # Search Blog for Tags
      if( isset($this->m_aRequest['action']) &&
              'tag' == $this->m_aRequest['action'] &&
              isset($this->m_aRequest['id']) &&
              !empty($this->m_aRequest['id'])) {
        $sWhere = "WHERE b.tags LIKE '%"	.
                Helper::formatHTMLCode($this->m_aRequest['id']).	"%'";
      }
      else {
        if(USERRIGHT < 3)
          $sWhere = "WHERE b.published = '1'";
      }

      $oEntries = new Query("SELECT COUNT(*) FROM blog "	.$sWhere);
      $this->_oPages = new Pages($this->m_aRequest, $oEntries->count(), $iLimit);

      $oGetData = new Query("	SELECT
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
															"	.$sWhere.	"
															GROUP BY
																b.id
															ORDER BY
																b.id DESC
															LIMIT
																"	.$this->_oPages->getOffset().	",
																"	.$this->_oPages->getLimit() );

      while($aRow = $oGetData->fetch()) {
        $iID = $aRow['id'];
        $aTags = explode(', ', $aRow['tags']);

        $this->_aData[$iID] = array('id' => $aRow['id'],
                'authorID' => $aRow['authorID'],
                'tags' => $aTags,
                'tags_sum' => (int)count($aTags),
                'title' => Helper::formatBBCode($aRow['title']),
                'content' => Helper::formatBBCode($aRow['content'], true),
                'date' => Helper::formatTimestamp($aRow['date']),
                'date_modified' => Helper::formatTimestamp($aRow['date_modified']),
                'uid' => $aRow['uid'],
                'name' => Helper::formatBBCode($aRow['name']),
                'surname' => Helper::formatBBCode($aRow['surname']),
                'avatar18' => Helper::getAvatar('user/18/', $aRow['authorID']),
                'avatar32' => Helper::getAvatar('user/32/', $aRow['authorID']),
                'avatar64' => Helper::getAvatar('user/64/', $aRow['authorID']),
                'comment_sum' => $aRow['commentSum'],
                'eTitle' => Helper::formatBBCode(urlencode($aRow['title'])),
                'published' => $aRow['published']
        );
      }
    }
    # There's an ID so choose between editing or displaying entry
    else {
      if(USERRIGHT < 3)
        $sWhere = "AND b.published = '1'";

      $this->_oPages = new Pages($this->m_aRequest, 1);
      $oGetData = new Query("	SELECT
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
																b.id = '" .$this->_iID.  "'
															"	.$sWhere.	"
															GROUP BY
																b.title
															LIMIT 1");

      $aRow = $oGetData->fetch();

      # Edit only
      if( $bEdit == true ) {
        # Do we use WYSIWYG or BB-Code?
        if( isset($this->m_aRequest['write_mode']) &&
                'wysiwyg' == $this->m_aRequest['write_mode'] )
          $sContent = Helper::formatBBCode($aRow['content']);
        else
          $sContent = Helper::removeSlahes($aRow['content']);

        $this->_aData = array(	'id' => $aRow['id'],
                'authorID' => $aRow['authorID'],
                'tags' => Helper::removeSlahes($aRow['tags']),
                'title' => Helper::removeSlahes($aRow['title']),
                'content' => $sContent,
                'date' => Helper::formatTimestamp($aRow['date']),
                'published' => $aRow['published']
        );
        unset($sContent);
      }
      # Give back blog entry
      else {
        $aTags = explode(', ', $aRow['tags']);
        $this->_aData[1] = array(	'id' => $aRow['id'],
                'authorID' => $aRow['authorID'],
                'tags' => $aTags,
                'tags_sum' => (int)count($aTags),
                'title' => Helper::formatBBCode($aRow['title']),
                'content' => Helper::formatBBCode($aRow['content'], true),
                'date' => Helper::formatTimestamp($aRow['date']),
                'date_modified' => Helper::formatTimestamp($aRow['date_modified']),
                'uid' => $aRow['uid'],
                'name' => Helper::formatBBCode($aRow['name']),
                'surname' => Helper::formatBBCode($aRow['surname']),
                'avatar' => '',
                'comment_sum' => $aRow['commentSum'],
                'eTitle' => Helper::formatBBCode(urlencode($aRow['title'])),
                'published' => $aRow['published']
        );
      }
    }
  }

  public final function getData($iID = '', $bEdit = false) {
    if( !empty($iID) )
      $this->_iID = (int)$iID;

    $this->_setData($bEdit);
    return $this->_aData;
  }

  public function create() {
    $this->m_aRequest['published']  = isset($this->m_aRequest['published']) ?
            $this->m_aRequest['published'] :
            0;

    return new Query("INSERT INTO
												blog(authorID, title, tags, content, published, date)
											VALUES(
												'"	.USERID.	"',
												'"	.Helper::formatHTMLCode($this->m_aRequest['title']).	"',
												'"	.Helper::formatHTMLCode($this->m_aRequest['tags']).	"',
												'"	.Helper::formatHTMLCode($this->m_aRequest['content'], false).	"',
												'"	.(int)$this->m_aRequest['published'].	"',
												'"	.time().	"')
												");
  }

  public function update($iID) {
    $iDateModified = (isset($this->m_aRequest['show_update']) && $this->m_aRequest['show_update'] == true) ?
            time() :
            '';

    $iPublished = (isset($this->m_aRequest['published']) && $this->m_aRequest['published'] == true) ?
            '1' :
            '0';

    $sUpdateAuthor = (isset($this->m_aRequest['show_update']) && $this->m_aRequest['show_update'] == true) ?
            ", authorID = '"	.USERID.	"'" :
            '';

    return new Query("UPDATE
												`blog`
											SET
												title = '"	.Helper::formatHTMLCode($this->m_aRequest['title'], false).	"',
												tags = '"	.Helper::formatHTMLCode($this->m_aRequest['tags'], false).	"',
												content = '"	.Helper::formatHTMLCode($this->m_aRequest['content'], false).	"',
												published = '"	.(int)$iPublished.	"',
												date_modified = '"	.$iDateModified.	"'
												"	.$sUpdateAuthor.	"
											WHERE
												`id` = '"	.$iID.	"'");
  }

  public final function destroy($iID) {
    new Query("DELETE FROM blog WHERE id = '"	.$iID.	"' LIMIT 1");
    new Query("DELETE FROM comment WHERE parentID = '"	.$iID.	"' AND parentCat = 'b'");
    return true;
  }
}

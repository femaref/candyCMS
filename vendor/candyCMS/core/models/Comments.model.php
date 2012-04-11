<?php

/**
 * Handle comment SQL requests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Core\Models;

use CandyCMS\Core\Helpers\AdvancedException;
use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\Pagination;
use CandyCMS\Plugins\FacebookCMS;
use PDO;

class Comments extends Main {

  /**
   * Get comment data.
   *
   * @access public
   * @param integer $iId blog ID to load data from
   * @param integer $iEntries number of comments for this blog ID
   * @param integer $iLimit comment limit, -1 is infinite
   * @return array data from _setData
   *
   */
  public function getData($iId, $iEntries, $iLimit) {
    $aInts  = array('id', 'parent_id', 'author_id', 'author_facebook_id', 'user_id');
    $aBools = array('use_gravatar');

    $this->oPagination = new Pagination($this->_aRequest, $iEntries, $iLimit);

    try {
      $sOrder = defined('COMMENTS_SORTING') && (COMMENTS_SORTING == 'ASC' || COMMENTS_SORTING == 'DESC') ?
              COMMENTS_SORTING :
              'ASC';
      $sLimit = $iLimit === -1 ? '' : 'LIMIT :offset, :limit';

      $oQuery = $this->_oDb->prepare("SELECT
                                        c.*,
                                        u.id AS user_id,
                                        u.name AS user_name,
                                        u.surname AS user_surname,
                                        u.email AS user_email,
                                        u.use_gravatar
                                      FROM
                                        " . SQL_PREFIX . "comments c
                                      LEFT JOIN
                                        " . SQL_PREFIX . "users u
                                      ON
                                        u.id=c.author_id
                                      WHERE
                                        c.parent_id = :parent_id
                                      ORDER BY
                                        c.date " . $sOrder . ",
                                        c.id " . $sOrder . "
                                      " . $sLimit);

      $oQuery->bindParam('parent_id', $iId, PDO::PARAM_INT);
      if ($iLimit !== -1) {
        $oQuery->bindParam('limit', $this->oPagination->getLimit(), PDO::PARAM_INT);
        $oQuery->bindParam('offset', $this->oPagination->getOffset(), PDO::PARAM_INT);
      }
      $oQuery->execute();

      $aResult = $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0019 - ' . $p->getMessage());
      exit('SQL error.');
    }

    foreach ($aResult as $aRow) {
      $this->_aData[$aRow['id']] = $aRow;
      $this->_formatForOutput($this->_aData[$aRow['id']], $aInts, $aBools, 'comments');
      $this->_aData[$aRow['id']]['url'] = '/' . $this->_aRequest['controller'] . '/' . $iId . '#' . $aRow['id'];
    }
    # We crawl the facebook avatars
    if (PLUGIN_FACEBOOK_APP_ID && class_exists('\CandyCMS\Plugins\FacebookCMS'))
      $this->_getFacebookAvatars($aResult);

    # Get comment number
    $iLoop = 1;
    foreach ($this->_aData as $aData) {
      $iId = $aData['id'];
      $this->_aData[$iId]['loop'] = $iLoop;
      ++$iLoop;
    }

    return $this->_aData;
  }

  /**
   * Get user profile images from Facebook if enabled.
   *
   * @access private
   * @param array $aResult comment data to search image from
   *
   */
  private function _getFacebookAvatars($aResult) {
    # We go through our data and get all facebook uids we want to query
    $aIds = array();
    foreach ($aResult as $aRow) {

      # Skip unnecessary data
      if (empty($aRow['author_facebook_id']))
        continue;

      else
        $aIds[(int)$aRow['id']] = $aRow['author_facebook_id'];
    }

    # Create a new facebook array with avatar urls and use Session as cache
    $aFacebookAvatarCache = $this->_aSession['facebook']->getUserAvatars($aIds, $this->_aSession);

    # Finally, we need to rebuild avatar data in main data array
    foreach ($aIds as $iId => $sFacebookId) {
      $this->_aData[$iId]['author']['avatar_64'] = $aFacebookAvatarCache[$sFacebookId]['pic_square_with_logo'];
      $this->_aData[$iId]['author']['url'] = $aFacebookAvatarCache[$sFacebookId]['profile_url'];
    }
  }

  /**
   * Create a comment.
   *
   * @access public
   * @return boolean status of query
   *
   */
  public function create() {
    $sAuthorName = isset($this->_aRequest['name']) ?
            Helper::formatInput($this->_aRequest['name']) :
            $this->_aSession['user']['full_name'];

    $sAuthorEmail = isset($this->_aRequest['email']) ?
            Helper::formatInput($this->_aRequest['email']) :
            $this->_aSession['user']['email'];

    $iFacebookId = isset($this->_aRequest['facebook_id']) ?
            Helper::formatInput($this->_aRequest['facebook_id']) :
            $this->_aSession['user']['facebook_id'];

    try {
      $oQuery = $this->_oDb->prepare("INSERT INTO
                                        " . SQL_PREFIX . "comments
                                        ( author_id,
                                          author_facebook_id,
                                          author_name,
                                          author_email,
                                          author_ip,
                                          content,
                                          date,
                                          parent_id)
                                      VALUES
                                        ( :author_id,
                                          :author_facebook_id,
                                          :author_name,
                                          :author_email,
                                          :author_ip,
                                          :content,
                                          :date,
                                          :parent_id )");

      $oQuery->bindParam('author_id', $this->_aSession['user']['id'], PDO::PARAM_INT);
      $oQuery->bindParam('author_facebook_id', $iFacebookId, PDO::PARAM_INT);
      $oQuery->bindParam('author_name', $sAuthorName, PDO::PARAM_STR);
      $oQuery->bindParam('author_email', $sAuthorEmail, PDO::PARAM_STR);
      $oQuery->bindParam('author_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']), PDO::PARAM_STR);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);
      $oQuery->bindParam('parent_id', $this->_aRequest['parent_id'], PDO::PARAM_INT);

      $bReturn = $oQuery->execute();
      parent::$iLastInsertId = Helper::getLastEntry('comments');

      return $bReturn;
    }
    catch (\PDOException $p) {
      try {
        $this->_oDb->rollBack();
      }
      catch (\Exception $e) {
        AdvancedException::reportBoth('0020 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0021 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }

  /**
   * Return the parent ID of a comment.
   *
   * @static
   * @access public
   * @param integer $iId comment ID to get data from
   * @return integer $aResult['parent_id']
   *
   */
  public static function getParentId($iId) {
    if (empty(parent::$_oDbStatic))
      parent::connectToDatabase();

    try {
      $oQuery = parent::$_oDbStatic->prepare("SELECT
                                                parent_id
                                              FROM
                                                " . SQL_PREFIX . "comments
                                              WHERE
                                                id = :id
                                              LIMIT 1");

      $oQuery->bindParam('id', $iId, PDO::PARAM_INT);
      $oQuery->execute();

      $aResult = $oQuery->fetch(PDO::FETCH_ASSOC);
      return $aResult['parent_id'];
    }
    catch (\PDOException $p) {
      AdvancedException::reportBoth('0103 - ' . $p->getMessage());
      exit('SQL error.');
    }
  }
}

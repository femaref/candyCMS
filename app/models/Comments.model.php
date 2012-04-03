<?php

/**
 * Handle comment SQL requests.
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
use CandyCMS\Plugin\Controller\FacebookCMS as FacebookCMS;
use PDO;

class Comments extends Main {

  /**
   * Get comment data.
   *
   * @access public
   * @param integer $iId blog ID to load data from
   * @param integer $iEntries number of comments for this blog ID
   * @param integer $iLimit comment limit
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

      $oQuery = $this->_oDb->prepare("SELECT
                                        c.*,
                                        u.name,
                                        u.surname,
                                        u.id AS user_id,
                                        u.use_gravatar,
                                        u.email
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
                                      LIMIT
                                        :offset,
                                        :limit");

      $oQuery->bindParam('parent_id', $iId, PDO::PARAM_INT);
      $oQuery->bindParam('limit', $this->oPagination->getLimit(), PDO::PARAM_INT);
      $oQuery->bindParam('offset', $this->oPagination->getOffset(), PDO::PARAM_INT);
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
    if (PLUGIN_FACEBOOK_APP_ID && class_exists('\CandyCMS\Plugin\Controller\FacebookCMS'))
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
    # We go through our data and get all facebook posts
    $sFacebookUids = '';
    foreach ($aResult as $aRow) {

      # Skip unnecessary data
      if (empty($aRow['author_facebook_id']))
        continue;

      else
        $sFacebookUids .= $aRow['author_facebook_id'] . ',';
    }

    # Create a new facebook array with avatar urls
    $aFacebookAvatarCache = array();
    $aFacebookAvatars = $this->_aSession['facebook']->getUserAvatar($sFacebookUids);

    foreach ($aFacebookAvatars as $aFacebookAvatar) {
      $iUid = $aFacebookAvatar['uid'];
      $aFacebookAvatarCache[$iUid]['pic_square_with_logo'] = $aFacebookAvatar['pic_square_with_logo'];
      $aFacebookAvatarCache[$iUid]['profile_url'] = $aFacebookAvatar['profile_url'];
    }

    # Finally, we need to rebuild avatar data in main data array
    foreach ($aResult as $aRow) {
      # Skip unnecessary data
      if (empty($aRow['author_facebook_id']))
        continue;

      else {
        $iId = $aRow['id'];
        $iAuthorFacebookId = $aRow['author_facebook_id'];

        $this->_aData[$iId]['avatar_64'] = $aFacebookAvatarCache[$iAuthorFacebookId]['pic_square_with_logo'];
        $this->_aData[$iId]['author_website'] = $aFacebookAvatarCache[$iAuthorFacebookId]['profile_url'];
      }
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
            '';
    $sAuthorEmail = isset($this->_aRequest['email']) ?
            Helper::formatInput($this->_aRequest['email']) :
            $this->_aSession['user']['email'];
    $iFacebookId = isset($this->_aRequest['facebook_id']) ?
            Helper::formatInput($this->_aRequest['facebook_id']) :
            '';

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
   * Delete a comment.
   *
   * @static
   * @access public
   * @param integer $iId ID to delete
   * @return boolean status of query
   * @todo remove this function and use main
   *
   */
  public function destroy($iId) {
    try {
      $oQuery = $this->_oDb->prepare("DELETE FROM
                                        " . SQL_PREFIX . "comments
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
        AdvancedException::reportBoth('0022 - ' . $e->getMessage());
      }

      AdvancedException::reportBoth('0023 - ' . $p->getMessage());
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

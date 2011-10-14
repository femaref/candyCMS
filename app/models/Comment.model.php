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
use CandyCMS\Helper\Page as Page;
use PDO;

class Comment extends Main {

  /**
   * Set comment data.
   *
   * @access private
   * @param integer $iId ID of blog post
   * @param integer $iEntries number of comments for this blog post
   * @param integer $iLimit comment limit
   * @return array data
   *
   */
  private function _setData($iId, $iEntries, $iLimit) {
    $this->oPage = new Page($this->_aRequest, $iEntries, $iLimit);

    try {
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
                                        c.date ASC,
                                        c.id ASC
                                      LIMIT
                                        :offset,
                                        :limit");

      $oQuery->bindParam('parent_id', $iId, PDO::PARAM_INT);
      $oQuery->bindParam('limit', $this->oPage->getLimit(), PDO::PARAM_INT);
      $oQuery->bindParam('offset', $this->oPage->getOffset(), PDO::PARAM_INT);
      $oQuery->execute();

      $aResult = & $oQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (AdvancedException $e) {
      $oDb->rollBack();
    }

    # Count the loops to display comment number
    $iLoop = 1;
    foreach ($aResult as $aRow) {
      $iId = $aRow['id'];

      $this->_aData[$iId] = $this->_formatForOutput($aRow, 'blog');
      $this->_aData[$iId]['loop'] = $iLoop;

      $iLoop++;
    }

    # We crawl the facebook avatars
    if (class_exists('\CandyCMS\Plugin\FacebookCMS'))
      $this->_getFacebookAvatars($aResult);

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
    $oFacebook = new FacebookCMS(array(
                'appId' => FACEBOOK_APP_ID,
                'secret' => FACEBOOK_SECRET,
            ));

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
    $aFacebookAvatars = $oFacebook->getUserAvatar($sFacebookUids);

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
    return $this->_setData($iId, $iEntries, $iLimit);
  }

  /**
   * Create a comment.
   *
   * @access public
   * @return boolean status of query
   * @override app/models/Main.model.php
   *
   */
  public function create() {
    $sAuthorName  = isset($this->_aRequest['name']) ? Helper::formatInput($this->_aRequest['name']) : '';
    $sAuthorEmail = isset($this->_aRequest['email']) ? Helper::formatInput($this->_aRequest['email']) : USER_EMAIL;
    $iFacebookId  = isset($this->_aRequest['facebook_id']) ? Helper::formatInput($this->_aRequest['facebook_id']) : '';

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

      $iAuthorId = USER_ID;
      $oQuery->bindParam('author_id', $iAuthorId, PDO::PARAM_INT);
      $oQuery->bindParam('author_facebook_id', $iFacebookId, PDO::PARAM_INT);
      $oQuery->bindParam('author_name', $sAuthorName, PDO::PARAM_STR);
      $oQuery->bindParam('author_email', $sAuthorEmail, PDO::PARAM_STR);
      $oQuery->bindParam('author_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $oQuery->bindParam('content', Helper::formatInput($this->_aRequest['content']), PDO::PARAM_STR);
      $oQuery->bindParam('date', time(), PDO::PARAM_INT);
      $oQuery->bindParam('parent_id', $this->_aRequest['parent_id'], PDO::PARAM_INT);

      return $oQuery->execute();
    }
    catch (AdvancedException $e) {
      $this->_oDb->rollBack();
    }
  }


  /**
   * Delete a comment.
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
                                        " . SQL_PREFIX . "comments
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
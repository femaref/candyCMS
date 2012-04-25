<?php

/**
 * The archive plugin lists all blog entries by month and date.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.5
 *
 */

namespace CandyCMS\Plugins;

use CandyCMS\Core\Helpers\Helper;
use CandyCMS\Core\Helpers\SmartySingleton;

final class Tagcloud {

  /**
   * Identifier for Template Replacements
   *
   * @var constant
   *
   */
  const IDENTIFIER = 'tagcloud';

  /**
   * Show the (cached) tagcloud.
   *
   * @final
   * @access public
   * @param array $aRequest
   * @param array $aSession
   * @return string HTML
   *
   */
  public final function show(&$aRequest, &$aSession) {
    $sTemplateDir   = Helper::getPluginTemplateDir('tagcloud', 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

    $oSmarty = SmartySingleton::getInstance();
    $oSmarty->setTemplateDir($sTemplateDir);
    $oSmarty->setCaching(SmartySingleton::CACHING_LIFETIME_SAVED);

    $sCacheId = WEBSITE_MODE . '|blogs|' . WEBSITE_LOCALE . '|tagcloud|' . substr(md5($aSession['user']['role']), 0 , 10);
    if (!$oSmarty->isCached($sTemplateFile, $sCacheId)) {

      $sBlogsModel = \CandyCMS\Core\Models\Main::__autoload('Blogs');
      $oModel = & new $sBlogsModel($aRequest, $aSession);

      // get all tags + how often they are used
      $aTags = array();
      $aSortableTags = array();
      $aData = $oModel->getData('', false, 0);
      foreach ($aData as $aRow) {
        foreach ($aRow['tags'] as $sTag) {
          if ($sTag) {
            // initialize, if tag did not appear before
            if (!$aTags[$sTag]) {
              $aTags[$sTag] = array($aRow);
              $aSortableTags[$sTag] = 1;
            }
            else {
              // update counter
              $aSortableTags[$sTag] = $aSortableTags[$sTag] + 1;
              $aTags[$sTag][] = $aRow;
            }
          }
        }
      }

      // order by appearance amount DESC
      arsort($aSortableTags);

      $aData = array();
      $iIndex = 0;
      foreach ($aSortableTags as $sTag => $iAmount) {
        $aData[$iIndex] = array(
            'title'       => $sTag,
            'amount'      => $iAmount,
            'blogentries' => $aTags[$sTag],
            'url'         => WEBSITE_URL . '/blogs/' . $sTag);
        $iIndex++;
      }

      $oSmarty->assign('data', $aData);
    }

    return $oSmarty->fetch($sTemplateFile, $sCacheId);
  }
}
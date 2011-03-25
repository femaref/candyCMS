<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

final class Page {
  private $_aRequest;
  private $_iLimit;
  private $_iOffset;
  private $_iPage;
  private $_iCount;
  private $_iCurrentPage;

  public final function __construct($aRequest, $iCount, $iLimit = 10) {
    $this->_aRequest	=& $aRequest;
    $this->_iCount    =& $iCount;
    $this->_iLimit    =& $iLimit;

    $this->_iCurrentPage = isset($this->_aRequest['page']) ? (int) $this->_aRequest['page'] : 1;
    $this->_iPage = ceil($this->_iCount / $this->_iLimit);

    if (!$this->_iPage)
      $this->_iPage = 1;

    if ($this->_iCurrentPage < 1)
      $this->_iCurrentPage = 1;

    if ($this->_iCurrentPage > $this->_iPage)
      $this->_iCurrentPage = $this->_iPage;

    $this->_iOffset = ($this->_iCurrentPage - 1) * $this->_iLimit;
  }

  public final function getOffset() {
    return $this->_iOffset;
  }

  public final function getLimit() {
    return $this->_iLimit;
  }

  public final function getCurrentPage() {
    return $this->_iCurrentPage;
  }

  public final function showPages($sURL, $sRssAction = 'blog') {
    $oSmarty = new Smarty();
    $oSmarty->assign('page_current', $this->_iCurrentPage);
    $oSmarty->assign('page_count', $this->_iPage);
    $oSmarty->assign('_action_url_', $sURL);
    $oSmarty->assign('_public_folder_', WEBSITE_CDN . '/public/images');

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = Helper::getTemplateDir('pages/show');
    return $oSmarty->fetch('pages/show.tpl');
  }

  public final function showSurrounding($sURL, $sRssAction = '') {
    $iNext = '';
    $iPrevious = '';

    if ($this->_iPage > 1 && $this->_iCurrentPage < $this->_iPage)
      $iNext = $this->_iCurrentPage + 1;

    if ($this->_iCurrentPage > 1)
      $iPrevious = $this->_iCurrentPage - 1;

    $oSmarty = new Smarty();
    $oSmarty->assign('_action_url_', $sURL);
    $oSmarty->assign('_page_limit_', $this->_iLimit);
    $oSmarty->assign('_page_count_', $this->_iCount);
    $oSmarty->assign('_page_next_', $iNext);
    $oSmarty->assign('_page_previous_', $iPrevious);
    $oSmarty->assign('_rss_section_', $sRssAction);

    # Language
    $oSmarty->assign('lang_next_entries', LANG_PAGES_NEXT_ENTRIES);
    $oSmarty->assign('lang_previous_entries', LANG_PAGES_PREVIOUS_ENTRIES);
    $oSmarty->assign('lang_rss_feed', LANG_GLOBAL_RSS);

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = Helper::getTemplateDir('pages/surrounding');
    return $oSmarty->fetch('pages/surrounding.tpl');
  }
}
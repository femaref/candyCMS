<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

class Page {

  private $_aRequest;
  private $_iLimit;
  private $_iOffset;
  private $_iPages;
  private $_iEntries;
  private $_iCurrentPage;

  public function __construct($aRequest, $iEntries, $iLimit = 10) {
    $this->_aRequest =& $aRequest;
    $this->_iEntries =& $iEntries;
    $this->_iLimit =& $iLimit;

    $this->_iPages = ceil($this->_iEntries / $this->_iLimit); # All pages
    $this->_iCurrentPage = isset($this->_aRequest['page']) && (int) $this->_aRequest['page'] <= $this->_iPages ? (int) $this->_aRequest['page'] : 1;

    if (!$this->_iPages)
      $this->_iPages = 1;

    if ($this->_iCurrentPage < 1)
      $this->_iCurrentPage = 1;

    if ($this->_iCurrentPage > $this->_iPages)
      $this->_iCurrentPage = $this->_iPages;

    if (isset($this->_aRequest['page']) && (int) $this->_aRequest['page'] > $this->_iPages) {
			header('Status: 404 Not Found');
      Helper::redirectTo('/error/404');
    }

    $this->_iOffset = ($this->_iCurrentPage - 1) * $this->_iLimit;
  }

  public function getOffset() {
    return $this->_iOffset;
  }

  public function getLimit() {
    return $this->_iLimit;
  }

  public function getCurrentPage() {
    return $this->_iCurrentPage;
  }

  public function showPages($sUrl) {
    $oSmarty = new Smarty();
    $oSmarty->assign('page_current', $this->_iCurrentPage);
    $oSmarty->assign('page_last', $this->_iPages);
    $oSmarty->assign('_action_url_', $sUrl);
    $oSmarty->assign('_public_folder_', WEBSITE_CDN . '/public/images');

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = Helper::getTemplateDir('pages/show');
    return $oSmarty->fetch('pages/show.tpl');
  }

  public function showSurrounding($sUrl, $sRssAction = '') {
    $iNext = '';
    $iPrevious = '';

    if ($this->_iPages > 1 && $this->_iCurrentPage < $this->_iPages)
      $iNext = $this->_iCurrentPage + 1;

    if ($this->_iCurrentPage > 1)
      $iPrevious = $this->_iCurrentPage - 1;

    $oSmarty = new Smarty();
    $oSmarty->assign('_action_url_', $sUrl);
    $oSmarty->assign('_page_entries_', $this->_iEntries);
    $oSmarty->assign('_page_limit_', $this->_iLimit);
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
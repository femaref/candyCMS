<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

final class Pages {
  private $m_aRequest;
  private $_iLimit;
  private $_iOffset;
  private $_iPages;
  private $_iCount;
  private $_iCurrentPage;

  public final function __construct($aRequest, $iCount, $iLimit = 10) {
    $this->m_aRequest	=& $aRequest;

    # Define temporary limit
    $this->_iCount =& $iCount;
    $this->_iLimit =& $iLimit;
    $this->_iCurrentPage = isset($this->m_aRequest['page']) ? (int)$this->m_aRequest['page'] : 1;
    $this->_iPages = ceil($this->_iCount / $this->_iLimit);

    if(!$this->_iPages)
      $this->_iPages = 1;

    if($this->_iCurrentPage < 1)
      $this->_iCurrentPage = 1;

    if($this->_iCurrentPage > $this->_iPages)
      $this->_iCurrentPage = $this->_iPages;

    $this->_iOffset	= ($this->_iCurrentPage - 1) * $this->_iLimit;
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
    $oSmarty->assign('current', $this->_iCurrentPage);
    $oSmarty->assign('URL', $sURL);
    $oSmarty->assign('ROOT', WEBSITE_CDN. '/' .PATH_IMAGES);
    $oSmarty->assign('RSS', $sRssAction);
    $oSmarty->assign('page', $this->_iPages);

    $oSmarty->template_dir = Helper::templateDir('pages/pages');
    return $oSmarty->fetch('pages/pages.tpl');
  }

  public final function showSurrounding($sURL, $sRssAction = '') {
    $iNext      = '';
    $iPrevious	= '';

    if($this->_iPages > 1 && $this->_iCurrentPage < $this->_iPages)
      $iNext = $this->_iCurrentPage + 1;

    if($this->_iCurrentPage > 1)
      $iPrevious = $this->_iCurrentPage - 1;

    $oSmarty = new Smarty();
    $oSmarty->assign('limit', $this->_iLimit);
    $oSmarty->assign('count', $this->_iCount);
    $oSmarty->assign('next', $iNext);
    $oSmarty->assign('previous', $iPrevious);
    $oSmarty->assign('URL', $sURL);
    $oSmarty->assign('RSS', $sRssAction);

    # Language
    $oSmarty->assign('lang_next_entries', LANG_PAGES_NEXT_ENTRIES);
    $oSmarty->assign('lang_previous_entries', LANG_PAGES_PREVIOUS_ENTRIES);
    $oSmarty->assign('lang_rss_feed', LANG_GLOBAL_RSS);

    $oSmarty->template_dir = Helper::templateDir('pages/surrounding');
    return $oSmarty->fetch('pages/surrounding.tpl');
  }
}
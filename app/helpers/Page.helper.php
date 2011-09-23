<?php

/**
 * Handle anything that has to do with pagination.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Helper;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;

class Page {

	/**
	 * Alias for $_REQUEST
	 *
	 * @var array
	 * @access private
	 */
  private $_aRequest;

	/**
	 * Limit of posts.
	 *
	 * @var integer
	 * @access private
	 */
  private $_iLimit;

	/**
	 * Entry offset.
	 *
	 * @var integer
	 * @access private
	 */
  private $_iOffset;

	/**
	 * Counted pages.
	 *
	 * @var integer
	 * @access private
	 */
  private $_iPages;

	/**
	 * Sum of entries.
	 *
	 * @var integer
	 * @access private
	 */
  private $_iEntries;

	/**
	 * Page that is currently shown.
	 *
	 * @var integer
	 * @access private
	 */
  private $_iCurrentPage;

	/**
	 * Initialize page helper.
	 *
	 * @access public
	 * @param array $aRequest alias for the combination of $_GET and $_POST
	 * @param integer $iEntries Sum of entries.
	 * @param integer $iLimit limit of entries per page.
	 *
	 */
  public function __construct($aRequest, $iEntries, $iLimit = 10) {
    $this->_aRequest  =& $aRequest;
    $this->_iEntries  =& $iEntries;
    $this->_iLimit    =& $iLimit;

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
      return Helper::redirectTo('/error/404');
    }

    $this->_iOffset = ($this->_iCurrentPage - 1) * $this->_iLimit;
  }

	/**
   * Return offset.
   *
   * @return integer $this->_iOffset
   *
   */
  public function getOffset() {
    return $this->_iOffset;
  }

	/**
   * Return entry limit.
   *
   * @return integer $this->_iLimit
   *
   */
  public function getLimit() {
    return $this->_iLimit;
  }

	/**
   * Return current page.
   *
   * @return integer $this->_iCurrentPage
   *
   */
  public function getCurrentPage() {
    return $this->_iCurrentPage;
  }

	/**
   * Show all page numbers as a link.
   *
   * @param string $sUrl section to show.
   * @return string HTML content
   *
   */
  public function showPages($sUrl = '') {
    $oSmarty = new \Smarty();
    $oSmarty->assign('page_current', $this->_iCurrentPage);
    $oSmarty->assign('page_last', $this->_iPages);
    $oSmarty->assign('_action_url_', !empty($sUrl) ? $sUrl : Helper::formatInput($this->_aRequest['section']));
    $oSmarty->assign('_public_folder_', WEBSITE_CDN . '/public/images');

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = Helper::getTemplateDir('pages', 'show');
    return $oSmarty->fetch('show.tpl');
  }

	/**
   * Show surrounding pages.
   *
   * @param string $sRssAction section to show for RSS
   * @return string HTML content
   *
   */
  public function showSurrounding($sRssAction = '') {
    $iNext = '';
    $iPrevious = '';

    if ($this->_iPages > 1 && $this->_iCurrentPage < $this->_iPages)
      $iNext = $this->_iCurrentPage + 1;

    if ($this->_iCurrentPage > 1)
      $iPrevious = $this->_iCurrentPage - 1;

    $oSmarty = new \Smarty();
    $oSmarty->assign('_page_entries_', $this->_iEntries);
    $oSmarty->assign('_page_limit_', $this->_iLimit);
    $oSmarty->assign('_page_next_', $iNext);
    $oSmarty->assign('_page_previous_', $iPrevious);
    $oSmarty->assign('_rss_section_', $sRssAction);

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
    $oSmarty->template_dir = Helper::getTemplateDir('pages', 'surrounding');
    return $oSmarty->fetch('surrounding.tpl');
  }
}
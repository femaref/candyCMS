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

class Pagination {

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
	 * Set up Smarty.
	 *
	 * @var object
	 * @access private
	 */
  private $_oSmarty;

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
			header("HTTP/1.0 404 Not Found");
      return Helper::redirectTo('/error/404');
    }

    $this->_iOffset = ($this->_iCurrentPage - 1) * $this->_iLimit;

    $this->_oSmarty = new \Smarty();
    $this->_oSmarty->cache_dir    = CACHE_DIR;
    $this->_oSmarty->compile_dir  = COMPILE_DIR;
  }

	/**
   * Return offset.
   *
   * @access public
   * @return integer $this->_iOffset
   *
   */
  public function getOffset() {
    return $this->_iOffset;
  }

	/**
   * Return entry limit.
   *
   * @access public
   * @return integer $this->_iLimit
   *
   */
  public function getLimit() {
    return $this->_iLimit;
  }

	/**
   * Return pages count.
   *
   * @access public
   * @return integer $this->_iPages
   *
   */
  public function getPages() {
    return $this->_iPages;
  }

	/**
   * Return current page.
   *
   * @access public
   * @return integer $this->_iCurrentPage
   *
   */
  public function getCurrentPage() {
    return $this->_iCurrentPage;
  }

	/**
   * Show all page numbers as a link.
   *
   * @access public
   * @param string $sUrl section to show.
   * @return string HTML content if there are more than one pages
   *
   */
  public function showPages($sUrl = '') {
		if($this->_iPages > 1) {
			$this->_oSmarty->assign('page_current', $this->_iCurrentPage);
			$this->_oSmarty->assign('page_last', $this->_iPages);
			$this->_oSmarty->assign('_action_url_', !empty($sUrl) ? $sUrl : Helper::formatInput($this->_aRequest['section']));
			$this->_oSmarty->assign('_public_folder_', WEBSITE_CDN . '/public/images');

			$this->_oSmarty->template_dir = Helper::getTemplateDir('pages', 'show');
			return $this->_oSmarty->fetch('show.tpl');
		}
  }

	/**
   * Show surrounding pages.
   *
   * @access public
   * @param string $sRssAction section to show for RSS
   * @return string HTML content if there are more than one pages
   *
   */
  public function showSurrounding($sRssAction = '') {
		if($this->_iPages > 1) {
			$iNext = '';
			$iPrevious = '';

			if ($this->_iPages > 1 && $this->_iCurrentPage < $this->_iPages)
				$iNext = $this->_iCurrentPage + 1;

			if ($this->_iCurrentPage > 1)
				$iPrevious = $this->_iCurrentPage - 1;

			# Set up language
			$oI18n = new I18n(WEBSITE_LANGUAGE);
			$this->_oSmarty->assign('lang', $oI18n->getArray());

			$this->_oSmarty->assign('_page_entries_', $this->_iEntries);
			$this->_oSmarty->assign('_page_limit_', $this->_iLimit);
			$this->_oSmarty->assign('_page_next_', $iNext);
			$this->_oSmarty->assign('_page_previous_', $iPrevious);
			$this->_oSmarty->assign('_rss_section_', $sRssAction);

			$this->_oSmarty->template_dir = Helper::getTemplateDir('pages', 'surrounding');
			return $this->_oSmarty->fetch('surrounding.tpl');
		}
  }
}
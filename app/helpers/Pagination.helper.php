<?php

/**
 * Handle anything that has to do with pagination.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Helper;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Helper\SmartySingleton as SmartySingleton;

class Pagination {

	/**
	 * Alias for $_REQUEST
	 *
	 * @var array
	 * @access private
	 *
	 */
  private $_aRequest;

	/**
	 * Limit of posts.
	 *
	 * @var integer
	 * @access private
	 *
	 */
  private $_iLimit;

	/**
	 * Entry offset.
	 *
	 * @var integer
	 * @access private
	 *
	 */
  private $_iOffset;

	/**
	 * Counted pages.
	 *
	 * @var integer
	 * @access private
	 *
	 */
  private $_iPages;

	/**
	 * Sum of entries.
	 *
	 * @var integer
	 * @access private
	 *
	 */
  private $_iEntries;

	/**
	 * Page that is currently shown.
	 *
	 * @var integer
	 * @access private
	 *
	 */
  private $_iCurrentPage;

	/**
	 * Set up Smarty.
	 *
	 * @var object
	 * @access private
	 *
	 */
  private $_oSmarty;

	/**
	 * Initialize page helper.
	 *
	 * @access public
	 * @param array $aRequest alias for the combination of $_GET and $_POST
	 * @param integer $iEntries sum of entries.
	 * @param integer $iLimit limit of entries per page.
	 *
	 */
  public function __construct(&$aRequest, $iEntries, $iLimit = 10) {
    $this->_aRequest	= & $aRequest;
		$this->_iEntries	= & $iEntries;
		$this->_iLimit		= & $iLimit;

    $this->_iPages = ceil($this->_iEntries / $this->_iLimit); # All pages
    $this->_iCurrentPage = isset($this->_aRequest['page']) && (int) $this->_aRequest['page'] <= $this->_iPages ?
						(int) $this->_aRequest['page'] :
						1;

    if (!$this->_iPages)
      $this->_iPages = 1;

    if ($this->_iCurrentPage < 1)
      $this->_iCurrentPage = 1;

    if ($this->_iCurrentPage > $this->_iPages)
      $this->_iCurrentPage = $this->_iPages;

    if (isset($this->_aRequest['page']) && (int) $this->_aRequest['page'] > $this->_iPages)
      return Helper::redirectTo('/errors/404');

    $this->_iOffset = ($this->_iCurrentPage - 1) * $this->_iLimit;

    $this->_oSmarty = SmartySingleton::getInstance();
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
   * Note that if you want to use ajax requests for loading pages, you have to set up $sController manually
   * and prefix it with a slash. Mainly that would be "showPages('/' . $this->_aRequest['controller'])".
   *
   * @access public
   * @param string $sController controller to show.
   * @return string HTML content if there are more than one pages
   *
   */
  public function showPages($sController = '') {
    if ($this->_iPages > 1) {
      $sTemplateDir  = Helper::getTemplateDir('paginations', 'show');
      $sTemplateFile = Helper::getTemplateType($sTemplateDir, 'show');

      $aPage = array(
          'last'       => $this->_iPages,
          'controller' => !empty($sController) ?
                  $sController :
                  Helper::formatInput($this->_aRequest['controller']));

      $this->_oSmarty->assign('_PAGE', $aPage);

      $this->_oSmarty->setTemplateDir($sTemplateDir);
      return $this->_oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
  }

	/**
   * Show surrounding pages.
   *
   * @access public
   * @param string $sController controller to show for RSS
   * @return string HTML content if there are more than one pages
   *
   */
  public function showSurrounding($sController = 'blogs') {
    $sTemplateDir  = Helper::getTemplateDir('paginations', 'surrounding');
    $sTemplateFile = Helper::getTemplateType($sTemplateDir, 'surrounding');

    if ($this->_iPages > 1 && $this->_iCurrentPage < $this->_iPages)
      $iNext = $this->_iCurrentPage + 1;

    if ($this->_iCurrentPage > 1)
      $iPrevious = $this->_iCurrentPage - 1;

    $aPage = array(
        'entries'     => $this->_iEntries,
        'limit'       => $this->_iLimit,
        'next'        => isset($iNext) ? $iNext : '',
        'previous'    => isset($iPrevious) ? $iPrevious : '',
        'controller'  => $sController);

    $this->_oSmarty->assign('_PAGE', $aPage);

    $this->_oSmarty->setTemplateDir($sTemplateDir);
    return $this->_oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }
}
<?php

/**
 * Start a search.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.5
 *
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Model\Search as Model;
use Smarty;

class Search extends Main {

	/**
	 * Search headline.
	 *
	 * @var string
	 * @access protected
	 */
  protected $_sHeadline;

	/**
	 * Search string.
	 *
	 * @var string
	 * @access protected
	 */
  protected $_sSearch;

	/**
	 * Include the search model.
	 *
	 * @access public
	 *
	 */
  public function __init() {
    $this->_sTemplateFolder = 'searches';

    require PATH_STANDARD . '/app/models/Search.model.php';

    $this->_oModel = new Model($this->_aRequest, $this->_aSession);
  }

	/**
	 * Show search results.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
  public function show() {
    if (!isset($this->_aRequest['search']) || empty($this->_aRequest['search']))
      return $this->showFormTemplate();

    else {
      $sString = & Helper::formatInput($this->_aRequest['search']);

      $this->_sHeadline = str_replace('%s', $sString, I18n::get('search.title.show'));

      $this->oSmarty->assign('string', $sString);
      $this->oSmarty->assign('tables', $this->_oModel->getData($sString,
							array('blogs', 'contents', 'downloads', 'gallery_albums')));

      # Create page title and description
      $this->setDescription($this->_sHeadline);
      $this->setTitle($this->_sHeadline);

      $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
  }

	/**
	 * Provide a search form template.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
  public function showFormTemplate() {
    $this->setDescription(I18n::get('global.search'));
    $this->setTitle(I18n::get('global.search'));

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

    $this->oSmarty->setTemplateDir($sTemplateDir);
    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }
}
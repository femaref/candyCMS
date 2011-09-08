<?php

/**
 * Start a search.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.5
 */

namespace CandyCMS\Controller;

require_once 'app/models/Search.model.php';

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
	 * @override app/controllers/Main.controller.php
	 *
	 */
  public function __init() {
    $this->_oModel = new \CandyCMS\Model\Search($this->_aRequest, $this->_aSession);
  }

	/**
	 * Show search results.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
  public function show() {
		$this->_setError('id', LANG_ERROR_FORM_MISSING_CONTENT);

		if (isset($this->_aError))
			return $this->showFormTemplate();

		else {
			$this->_oSmarty->assign('_search_', $this->getSearch());

			# Create page title and description
			$this->_setDescription($this->_sHeadline);
			$this->_setTitle($this->_sHeadline);

			# Language
			$this->_oSmarty->assign('lang_headline', $this->_sHeadline);

			$this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('searches', 'show');
			return $this->_oSmarty->fetch('show.tpl');
		}
	}

	/**
	 * Get search results.
	 * This method is also used by the Error controller to display alternative entries.
	 *
	 * @access public
	 * @param string $sTitle Optional string we search for instead of title and content. ($_REQUEST['seo_title])
	 * @return string HTML content
	 * @see app/controllers/Error.controller.php
	 *
	 */
  public function getSearch($sTitle = '') {
    $aTables = array('blogs', 'contents', 'downloads', 'gallery_albums');
    $this->_sSearch = empty($sTitle) ? \CandyCMS\Helper\Helper::formatInput($this->_aRequest['id']) : \CandyCMS\Helper\Helper::formatInput($sTitle);
    $this->_sHeadline = str_replace('%s', $this->_sSearch, LANG_SEARCH_SHOW_TITLE);

    $this->_oSmarty->assign('search', $this->_sSearch);
    $this->_oSmarty->assign('tables', $this->_oModel->getData($this->_sSearch, $aTables));

    $this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('searches', '_show');
    return $this->_oSmarty->fetch('_show.tpl');
  }

	/**
	 * Provide form template
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
  public function showFormTemplate() {
    $this->_setDescription(LANG_GLOBAL_SEARCH);
    $this->_setTitle(LANG_GLOBAL_SEARCH);

    $this->_oSmarty->assign('lang_terms', LANG_SEARCH_SHOW_LABEL_TERMS);

    $this->_oSmarty->template_dir = \CandyCMS\Helper\Helper::getTemplateDir('searches', '_form');
    return $this->_oSmarty->fetch('_form.tpl');
  }
}
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

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Search as Model;

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
		$this->_setError('id', LANG_ERROR_FORM_MISSING_CONTENT);

		if (isset($this->_aError))
			return $this->showFormTemplate();

		else {
			$this->oSmarty->assign('_search_', $this->getSearch());

			# Create page title and description
			$this->_setDescription($this->_sHeadline);
			$this->_setTitle($this->_sHeadline);

			# Language
			$this->oSmarty->assign('lang_headline', $this->_sHeadline);

			$this->oSmarty->template_dir = Helper::getTemplateDir('searches', 'show');
			return $this->oSmarty->fetch('show.tpl');
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
    $this->_sSearch = empty($sTitle) ? Helper::formatInput($this->_aRequest['id']) : Helper::formatInput($sTitle);
    $this->_sHeadline = str_replace('%s', $this->_sSearch, LANG_SEARCH_SHOW_TITLE);

    $this->oSmarty->assign('search', $this->_sSearch);
    $this->oSmarty->assign('tables', $this->_oModel->getData($this->_sSearch, $aTables));

    $this->oSmarty->template_dir = Helper::getTemplateDir('searches', '_show');
    return $this->oSmarty->fetch('_show.tpl');
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

    $this->oSmarty->assign('lang_terms', LANG_SEARCH_SHOW_LABEL_TERMS);

    $this->oSmarty->template_dir = Helper::getTemplateDir('searches', '_form');
    return $this->oSmarty->fetch('_form.tpl');
  }
}
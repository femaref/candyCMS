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
use Smarty;

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
    if (!isset($this->_aRequest['id']) || empty($this->_aRequest['id']))
      return $this->showFormTemplate();

    else {
      $sString = Helper::formatInput($this->_aRequest['id']);

      $aTables = array('blogs', 'contents', 'downloads', 'gallery_albums');
      $this->_sHeadline = str_replace('%s', $sString, $this->oI18n->get('search.title.show'));

      $this->oSmarty->assign('string', $sString);
      $this->oSmarty->assign('tables', $this->_oModel->getData($sString, $aTables));

      # Create page title and description
      $this->_setDescription($this->_sHeadline);
      $this->_setTitle($this->_sHeadline);

      $sTemplateDir = Helper::getTemplateDir('searches', 'show');
      $this->oSmarty->template_dir = $sTemplateDir;
      return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'show'));
    }
  }

	/**
	 * Provide form template
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
  public function showFormTemplate() {
    $this->_setDescription($this->oI18n->get('global.search'));
    $this->_setTitle($this->oI18n->get('global.search'));

    $sTemplateDir = Helper::getTemplateDir('searches', '_form');
    $this->oSmarty->template_dir = $sTemplateDir;
    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, '_form'));
  }
}
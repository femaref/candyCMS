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

class Searches extends Main {

	/**
	 * Search string.
	 *
	 * @var string
	 * @access protected
	 */
  protected $_sSearch;

	/**
	 * Show search results.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
  protected function _show() {
    if (!isset($this->_aRequest['search']) || !$this->_aRequest['search'])
      return $this->_create();

    else {
      $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

      $sString = Helper::formatInput($this->_aRequest['search']);

      if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
        $this->oSmarty->assign('string', $sString);
        $this->oSmarty->assign('tables', $this->_oModel->getData($sString,
                array('blogs', 'contents', 'downloads', 'gallery_albums')));

        $this->setTitle(str_replace('%s', $sString, I18n::get('searches.title.show')));
        $this->setDescription(str_replace('%s', $sString, I18n::get('searches.description.show')));
      }

      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
  }

  /**
   * show the search form, this is a create action since it creates a new search.
   *
   * @access protected
   * @return string HTML content.
   *
   */
  protected function _create() {
    $this->oSmarty->setCaching(false);
    return $this->_formTemplate();
  }

	/**
	 * Provide a search form template.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
  protected function _formTemplate() {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

    $this->setTitle(I18n::get('global.search'));
    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }
}
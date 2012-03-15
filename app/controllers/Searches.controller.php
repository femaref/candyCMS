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
use Smarty;

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
    if (!isset($this->_aRequest['search']) || empty($this->_aRequest['search']))
      return $this->_showFormTemplate();

    else {
      $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

      $sString = Helper::formatInput($this->_aRequest['search']);
      $sHeadline = str_replace('%s', $sString, I18n::get('search.title.show'));

      $this->oSmarty->assign('string', $sString);
      $this->oSmarty->assign('tables', $this->_oModel->getData($sString,
							array('blogs', 'contents', 'downloads', 'gallery_albums')));

      $this->setDescription($sHeadline);
      $this->setTitle($sHeadline);

      $this->oSmarty->setTemplateDir($sTemplateDir);
      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
  }

	/**
	 * Provide a search form template.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
  protected function _showFormTemplate() {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

    $this->oSmarty->setTemplateDir($sTemplateDir);
    $this->setDescription(I18n::get('global.search'));
    $this->setTitle(I18n::get('global.search'));

    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }
}
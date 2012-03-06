<?php

/**
 * Show customized error message when page is not found.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Controller;

use CandyCMS\Controller\Search as Search;
use CandyCMS\Helper\Helper as Helper;
use Smarty;

class Error extends Main {

	/**
	 * Show a 404 error when a page is not available or found.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
	public function show404() {
    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, '404');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '404');

		$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
	}
}
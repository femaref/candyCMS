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

use CandyCMS\Helper\Helper as Helper;
//use CandyCMS\Helper\SmartySingleton as SmartySingleton;

class Errors extends Main {

	/**
	 * Show a 404 error when a page is not available or found.
	 *
	 * @access protected
	 * @param string $sError error to display
	 * @return string HTML content
	 *
	 */
	protected function _show($sError = '404') {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], $sError);
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, $sError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
	}
}
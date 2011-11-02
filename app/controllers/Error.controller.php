<?php

/**
 * Show customized error message when page is not found.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Controllers\Search as Search;
use CandyCMS\Helper\Helper as Helper;

class Error extends Main {

	/**
	 * Show a 404 error when a page is not avaiable or found.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
	public function show404() {
		if (isset($this->_aRequest['seo_title'])) {
      $this->__autoload('Search');

			$oSearch = new Search($this->_aRequest, $this->_aSession);
			$oSearch->__init();
			$this->oSmarty->assign('_search_', $oSearch->getSearch(urldecode($this->_aRequest['seo_title'])));
		}

    $sTemplateDir = Helper::getTemplateDir('errors', '404');
    $this->oSmarty->template_dir = $sTemplateDir;
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, '404'));
	}
}
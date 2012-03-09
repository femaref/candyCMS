<?php

/**
 * Print out sitemap as HTML or XML.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Blog as Model_Blog;
use CandyCMS\Model\Content as Model_Content;
use CandyCMS\Model\Download as Model_Download;
use CandyCMS\Model\Gallery as Model_Gallery;
use Smarty;

class Sitemap extends Main {

	/**
	 * Show the sitemap as XML. Site is cached for one hour.
	 *
	 * @access protected
	 * @return string XML content
	 *
	 */
	protected function _showXML() {
		Header('Content-Type: text/xml');

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'xml');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'xml');

		$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
		$this->oSmarty->setCacheLifetime(1800); # 30 minutes

		if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
			$this->oSmarty->assign('_website_landing_page_', WEBSITE_URL . '/' . WEBSITE_LANDING_PAGE);
			$this->_getSitemap();
		}

    $this->oSmarty->setTemplateDir($sTemplateDir);
    $this->oSmarty->display($sTemplateFile, UNIQUE_ID);
		exit();
	}

	/**
	 * Show the sitemap as HTML. Site is cached for one minute.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
	protected function _show() {
    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'show');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

		$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
		$this->oSmarty->setCacheLifetime(180);

		if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID))
			$this->_getSitemap();

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
	}

	/**
	 * Generate the sitemap. Query tables and build structure.
	 *
	 * @access private
	 *
	 */
	private function _getSitemap() {
		$sModel		= $this->__autoload('Blog', true);
		$oBlog		= new $sModel($this->_aRequest, $this->_aSession);

		$sModel		= $this->__autoload('Content', true);
		$oContent = new $sModel($this->_aRequest, $this->_aSession);

		$sModel		= $this->__autoload('Gallery', true);
		$oGallery = new $sModel($this->_aRequest, $this->_aSession);

		$this->oSmarty->assign('blog', $oBlog->getData('', false, 1000));
		$this->oSmarty->assign('content', $oContent->getData('', false, 1000));
		$this->oSmarty->assign('gallery', $oGallery->getData('', false, false, 1000));
	}
}
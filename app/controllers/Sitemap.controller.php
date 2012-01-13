<?php

/**
 * Print out sitemap as HTML or XML.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Blog as Model_Blog;
use CandyCMS\Model\Content as Model_Content;
use CandyCMS\Model\Download as Model_Download;
use CandyCMS\Model\Gallery as Model_Gallery;
use Smarty;

require_once 'app/models/Blog.model.php';
require_once 'app/models/Content.model.php';
require_once 'app/models/Download.model.php';
require_once 'app/models/Gallery.model.php';

class Sitemap extends Main {

	/**
	 * Show the sitemap as XML. Site is cached for one hour.
	 *
	 * @access public
	 * @return string XML content
	 *
	 */
	public function showXML() {
		Header('Content-Type: text/xml');

		$sTemplateDir = Helper::getTemplateDir('sitemaps', 'xml');
		$this->oSmarty->template_dir = $sTemplateDir;
		$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
		$this->oSmarty->setCacheLifetime(1800); # 30 minutes

		if (!$this->oSmarty->isCached('show')) {
			$this->oSmarty->assign('_website_landing_page_', WEBSITE_URL . '/' . WEBSITE_LANDING_PAGE);
			$this->_getSitemap();
		}

		return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'xml'));
	}

	/**
	 * Show the sitemap as HTML. Site is cached for one minute.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
	public function show() {
		$sTemplateDir = Helper::getTemplateDir('sitemaps', 'show');
		$this->oSmarty->template_dir = $sTemplateDir;
		$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
		$this->oSmarty->setCacheLifetime(60);

		if (!$this->oSmarty->isCached('show'))
			$this->_getSitemap();

		return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'show'));
	}

	/**
	 * Generate the sitemap. Query tables and build structure.
	 *
	 * @access private
	 *
	 */
	private function _getSitemap() {
		$oBlog = new Model_Blog($this->_aRequest, $this->_aSession);
		$aBlog = $oBlog->getData('', false, 1000);

		$oContent = new Model_Content($this->_aRequest, $this->_aSession);
		$aContent = $oContent->getData('', false, 1000);

		$oGallery = new Model_Gallery($this->_aRequest, $this->_aSession);
		$aGallery = $oGallery->getData('', false, false, 1000);

		#$oDownload = new Model_Download($this->_aRequest, $this->_aSession);
		#$aDownload = $oDownload->getData('', false);

		$this->oSmarty->assign('blog', $aBlog);
		$this->oSmarty->assign('content', $aContent);
		$this->oSmarty->assign('gallery', $aGallery);
	}
}
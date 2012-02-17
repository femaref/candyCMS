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

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'xml');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'xml');

		$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
		$this->oSmarty->setCacheLifetime(1800); # 30 minutes

		if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
			$this->oSmarty->assign('_website_landing_page_', WEBSITE_URL . '/' . WEBSITE_LANDING_PAGE);
			$this->_getSitemap();
		}

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
	}

	/**
	 * Show the sitemap as HTML. Site is cached for one minute.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
	public function show() {
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
   * @todo create __init() method
	 *
	 */
	private function _getSitemap() {
    require PATH_STANDARD . '/app/models/Blog.model.php';
    require PATH_STANDARD . '/app/models/Content.model.php';
    require PATH_STANDARD . '/app/models/Download.model.php';
    require PATH_STANDARD . '/app/models/Gallery.model.php';

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
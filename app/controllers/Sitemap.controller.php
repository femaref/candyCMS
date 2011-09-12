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
use CandyCMS\Model\Gallery as Model_Gallery;

require_once 'app/models/Blog.model.php';
require_once 'app/models/Content.model.php';
require_once 'app/models/Gallery.model.php';

class Sitemap extends Main {

	/**
	 * Show the sitemap as XML.
	 *
	 * @access public
	 * @return string XML content
	 *
	 */
	public function showXML() {
		Header('Content-Type: text/xml');

		$this->oSmarty->assign('_website_landing_page_', WEBSITE_URL . '/' . WEBSITE_LANDING_PAGE);
		$this->oSmarty->assign('_website_url_', WEBSITE_URL);

		$this->_getSitemap();

		$this->oSmarty->template_dir = Helper::getTemplateDir('sitemaps', 'xml');
		return $this->oSmarty->fetch('xml.tpl');
	}

	/**
	 * Show the sitemap as HTML.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
	public function show() {
		$this->_getSitemap();

		$this->oSmarty->template_dir = Helper::getTemplateDir('sitemaps', 'show');
		return $this->oSmarty->fetch('show.tpl');
	}

	/**
	 * Generate the sitemap. Query tables and build structure.
	 *
	 * @access private
	 *
	 */
	private function _getSitemap() {
		$oBlog = new Model_Blog();
		$aBlog = $oBlog->getData('', false, 1000);

		$oContent = new Model_Content();
		$aContent = $oContent->getData('', false, 1000);

		$oGallery = new Model_Gallery();
		$aGallery = $oGallery->getData('', false, false, 1000);

		$this->oSmarty->assign('blog', $aBlog);
		$this->oSmarty->assign('content', $aContent);
		$this->oSmarty->assign('gallery', $aGallery);
	}
}
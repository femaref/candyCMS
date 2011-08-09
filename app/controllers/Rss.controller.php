<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Blog.model.php';
require_once 'app/models/Gallery.model.php';
require_once 'app/helpers/Page.helper.php';

class Rss extends Main {

	public function __init() {
    Header('Content-Type: application/rss+xml');

		$this->_sSection = isset($this->_aRequest['template']) ?
						(string) strtolower($this->_aRequest['template']) :
						'Blog';

    # We might need to add a file extension to the rss feed
    if (substr($this->_sSection, 0, 4) == 'blog')
      $this->_sSection = 'blog';

	}

	public function show() {
		if($this->_sSection == 'blog') {
			$this->_oModel = new Model_Blog($this->_aRequest, $this->_aSession);
      $this->_aData = $this->_oModel->getData();
      $this->_setTitle(LANG_GLOBAL_BLOG . ' - ' . WEBSITE_NAME);

			return $this->_showDefault();
		}

		elseif($this->_sSection == 'gallery' && $this->_iId > 0) {
			$this->_oModel = new Model_Gallery($this->_aRequest, $this->_aSession);
      $this->_aData = $this->_oModel->getData($this->_iId, false, true);

      $this->_setTitle(Helper::removeSlahes(LANG_GLOBAL_GALLERY.	': '	.
              $this->_aData[$this->_iId]['title']));

			return $this->_showGallery();
		}

		else {
      header('Status: 404 Not Found');
      die(LANG_ERROR_GLOBAL_404_TITLE);
		}
	}

	private function _showDefault() {
		$this->_oSmarty->assign('_section_', $this->_sSection);
		$this->_oSmarty->assign('data', $this->_aData);
		$this->_oSmarty->assign('_title_', $this->getTitle());

		$this->_oSmarty->template_dir = Helper::getTemplateDir('rss', 'default');
		return $this->_oSmarty->fetch('default.tpl');
	}

	private function _showGallery() {
    $aData = $this->_aData[$this->_iId]['files'];
    rsort($aData);

		$this->_oSmarty->assign('_copyright_', $this->_aData[$this->_iId]['full_name']);
		$this->_oSmarty->assign('_content_', $this->_aData[$this->_iId]['content']);
		$this->_oSmarty->assign('_locale_', WEBSITE_LOCALE);
		$this->_oSmarty->assign('_link_', $this->_aData[$this->_iId]['url']);
		$this->_oSmarty->assign('_pubdate_', $this->_aData[$this->_iId]['date_rss']);
		$this->_oSmarty->assign('_section_', $this->_sSection);
		$this->_oSmarty->assign('_title_', $this->getTitle());

		$this->_oSmarty->assign('data', $aData);

		$this->_oSmarty->template_dir = Helper::getTemplateDir('rss', 'gallery');
		return $this->_oSmarty->fetch('gallery.tpl');
	}
}
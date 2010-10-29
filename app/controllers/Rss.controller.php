<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Blog.model.php';
require_once 'app/models/Gallery.model.php';
require_once 'app/helpers/Pages.helper.php';

class Rss extends Main {

	public function __init() {
		$this->_sSection = isset($this->_aRequest['template']) ?
						(string) ucfirst($this->_aRequest['template']) :
						'Blog';
	}

	public function show() {
		if($this->_sSection == 'Blog') {
			$this->_oModel = new Model_Blog($this->_aRequest, $this->_aSession);
      $this->_aData = $this->_oModel->getData();
			return $this->_showDefault();
		}
		elseif($this->_sSection == 'Gallery') {
			$this->_oModel = new Model_Gallery($this->_aRequest, $this->_aSession);
      $this->_aData = $this->_oModel->getData($this->_iId, false, true);

      $this->_setTitle(Helper::removeSlahes(LANG_GLOBAL_GALLERY.	': '	.
              $this->_aData[$this->_iId]['title']));

			return $this->_showGallery();
		}
	}

	private function _showDefault() {
		$oSmarty = new Smarty();
		$oSmarty->assign('_language_', str_replace('_', '-', strtolower(DEFAULT_LANGUAGE)));
		$oSmarty->assign('_pubdate_', date('r'));
		$oSmarty->assign('_section_', $this->_sSection);

		$oSmarty->assign('WEBSITE_NAME', WEBSITE_NAME);
		$oSmarty->assign('WEBSITE_SLOGAN', LANG_WEBSITE_SLOGAN);
		$oSmarty->assign('WEBSITE_URL', WEBSITE_URL);
		$oSmarty->assign('data', $this->_aData);

		# Language
		$oSmarty->assign('lang_website_title', LANG_WEBSITE_TITLE);

		$oSmarty->cache_dir = CACHE_DIR;
		$oSmarty->compile_dir = COMPILE_DIR;
		$oSmarty->template_dir = Helper::getTemplateDir('rss/default');
		return $oSmarty->fetch('rss/default.tpl');
	}

	private function _showGallery() {
		$oSmarty = new Smarty();

    $aData = $this->_aData[$this->_iId]['files'];
    rsort($aData);

		$oSmarty->assign('_copyright_', $this->_aData[$this->_iId]['full_name']);
		$oSmarty->assign('_description_', $this->_aData[$this->_iId]['description']);
		$oSmarty->assign('_language_', str_replace('_', '-', strtolower(DEFAULT_LANGUAGE)));
		$oSmarty->assign('_link_', $this->_aData[$this->_iId]['url']);
		$oSmarty->assign('_pubdate_', $this->_aData[$this->_iId]['date_rss']);
		$oSmarty->assign('_section_', $this->_sSection);
		$oSmarty->assign('_title_', $this->getTitle());

		$oSmarty->assign('data', $aData);

		# Language
		$oSmarty->assign('lang_website_title', $this->getTitle());

		$oSmarty->cache_dir = CACHE_DIR;
		$oSmarty->compile_dir = COMPILE_DIR;
		$oSmarty->template_dir = Helper::getTemplateDir('rss/gallery');
		return $oSmarty->fetch('rss/gallery.tpl');
	}
}
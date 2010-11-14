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

		elseif($this->_sSection == 'Gallery' && $this->_iId > 0) {
			$this->_oModel = new Model_Gallery($this->_aRequest, $this->_aSession);
      $this->_aData = $this->_oModel->getData($this->_iId, false, true);

      $this->_setTitle(Helper::removeSlahes(LANG_GLOBAL_GALLERY.	': '	.
              $this->_aData[$this->_iId]['title']));

			return $this->_showGallery();
		}

		else {
      header('Status: 404 Not Found');
      die(LANG_ERROR_GLOBAL_404);
		}
	}

	private function _showDefault() {
		$this->_oSmarty->assign('_section_', $this->_sSection);
		$this->_oSmarty->assign('data', $this->_aData);

		$this->_oSmarty->template_dir = Helper::getTemplateDir('rss/default');
		return $this->_oSmarty->fetch('rss/default.tpl');
	}

	private function _showGallery() {
    $aData = $this->_aData[$this->_iId]['files'];
    rsort($aData);

		$this->_oSmarty->assign('_copyright_', $this->_aData[$this->_iId]['full_name']);
		$this->_oSmarty->assign('_description_', $this->_aData[$this->_iId]['description']);
		$this->_oSmarty->assign('_locale_', WEBSITE_LOCALE);
		$this->_oSmarty->assign('_link_', $this->_aData[$this->_iId]['url']);
		$this->_oSmarty->assign('_pubdate_', $this->_aData[$this->_iId]['date_rss']);
		$this->_oSmarty->assign('_section_', $this->_sSection);
		$this->_oSmarty->assign('_title_', $this->getTitle());

		$this->_oSmarty->assign('data', $aData);

		$this->_oSmarty->template_dir = Helper::getTemplateDir('rss/gallery');
		return $this->_oSmarty->fetch('rss/gallery.tpl');
	}
}
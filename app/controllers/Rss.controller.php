<?php

/**
 * Show blog entries or gallery album files as RSS feed.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Model\Blog as Model_Blog;
use CandyCMS\Model\Gallery as Model_Gallery;
use Smarty;

class Rss extends Main {

  /**
   * Define the content type as RSS.
   *
   * @access public
   *
   */
  public function __init() {
    # Override template folder.
    $this->_sTemplateFolder = 'rss';

    Header('Content-Type: application/rss+xml');
    $this->_sSection = isset($this->_aRequest['subsection']) ?
            (string) strtolower($this->_aRequest['subsection']) :
            'blog';

    # Empty page and search to avoid news filters.
    unset($this->_aRequest['page'], $this->_aRequest['search']);

    require PATH_STANDARD . '/app/models/Blog.model.php';
    require PATH_STANDARD . '/app/models/Gallery.model.php';
  }

  /**
   * Show RSS feed.
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    # Blog
    if ($this->_sSection == 'blog') {
      $this->_oModel  = new Model_Blog($this->_aRequest, $this->_aSession);
      $this->_aData   = $this->_oModel->getData();

      $this->_setTitle(I18n::get('global.blog') . ' - ' . WEBSITE_NAME);

      return $this->_showDefault();
    }
    # Gallery
    elseif ($this->_sSection == 'gallery' && $this->_iId > 0) {
      $this->_oModel  = new Model_Gallery($this->_aRequest, $this->_aSession);
      $this->_aData   = $this->_oModel->getData($this->_iId, false, true);

      $this->_setTitle(I18n::get('global.gallery') . ': ' .
                      $this->_aData[$this->_iId]['title']);

      return $this->_showMedia();
    }
    else
      return Helper::redirectTo('/error/404');
  }

  /**
   * Show default RSS template. Save in cache for one minute.
   *
   * @access private
   * @return string HTML content
   *
   */
  private function _showDefault() {
    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'default');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'default');

		$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
		$this->oSmarty->setCacheLifetime(60);

		if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
			$this->oSmarty->assign('data', $this->_aData);
			$this->oSmarty->assign('_section_', $this->_sSection);
			$this->oSmarty->assign('_title_', $this->getTitle());
		}

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
	}

  /**
   * Show media RSS template
   *
   * @access private
   * @return string HTML content
   *
   */
  private function _showMedia() {
    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'media');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'media');

    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $this->oSmarty->setCacheLifetime(60);

    if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
      $aData = $this->_aData[$this->_iId]['files'];
      rsort($aData);

      $this->oSmarty->assign('_copyright_', $this->_aData[$this->_iId]['full_name']);
      $this->oSmarty->assign('_content_', $this->_aData[$this->_iId]['content']);
      $this->oSmarty->assign('_locale_', WEBSITE_LOCALE);
      $this->oSmarty->assign('_link_', $this->_aData[$this->_iId]['url']);
      $this->oSmarty->assign('_pubdate_', $this->_aData[$this->_iId]['date_rss']);
      $this->oSmarty->assign('_section_', $this->_sSection);
      $this->oSmarty->assign('_title_', $this->getTitle());

      $this->oSmarty->assign('data', $aData);
    }

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }
}
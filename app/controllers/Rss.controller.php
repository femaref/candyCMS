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
use CandyCMS\Model\Blog as Model_Blog;
use CandyCMS\Model\Gallery as Model_Gallery;
use Smarty;

require_once 'app/models/Blog.model.php';
require_once 'app/models/Gallery.model.php';

class Rss extends Main {

  /**
   * Define the content type as RSS.
   *
   * @access public
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init() {
    Header('Content-Type: application/rss+xml');
    $this->_sSection = isset($this->_aRequest['subsection']) ?
            (string) strtolower($this->_aRequest['subsection']) :
            'blog';

    # Empty page and search to avoid news filters.
    unset($this->_aRequest['page'], $this->_aRequest['search']);
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

      $this->_setTitle($this->oI18n->get('global.blog') . ' - ' . WEBSITE_NAME);

      return $this->_showDefault();
    }
    # Gallery
    elseif ($this->_sSection == 'gallery' && $this->_iId > 0) {
      $this->_oModel  = new Model_Gallery($this->_aRequest, $this->_aSession);
      $this->_aData   = $this->_oModel->getData($this->_iId, false, true);

      $this->_setTitle(Helper::removeSlahes($this->oI18n->get('global.gallery') . ': ' .
                      $this->_aData[$this->_iId]['title']));

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
		$sTemplateDir = Helper::getTemplateDir('rss', 'default');
		$this->oSmarty->template_dir = $sTemplateDir;
		$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
		$this->oSmarty->setCacheLifetime(60);

		if (!$this->oSmarty->isCached('default')) {
			$this->oSmarty->assign('data', $this->_aData);
			$this->oSmarty->assign('_section_', $this->_sSection);
			$this->oSmarty->assign('_title_', $this->getTitle());
		}

		return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'default'), WEBSITE_LANGUAGE);
	}

  /**
   * Show media RSS template
   *
   * @access private
   * @return string HTML content
   * @todo gallery and media rss differences
   *
   */
  private function _showMedia() {
    $sTemplateDir = Helper::getTemplateDir('rss', 'media');
    $this->oSmarty->template_dir = $sTemplateDir;
    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $this->oSmarty->setCacheLifetime(60);

    if (!$this->oSmarty->isCached('media')) {
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

    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'media'), WEBSITE_LANGUAGE . '|' . $this->_iId);
  }
}
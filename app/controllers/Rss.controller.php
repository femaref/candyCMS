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
use Smarty;

class Rss extends Main {

  /**
   * Define the content type as RSS.
   *
   * @access public
   *
   */
  public function __init() {
    Header('Content-Type: application/rss+xml');
  }

  /**
   * Show RSS feed.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    if ($this->_aRequest['section'] == 'galleries' && $this->_iId > 0)
      $this->_showMedia();

    else
      $this->_showDefault();
  }

  /**
   * Show default RSS template. Save in cache for one minute.
   *
   * @access private
   * @return string HTML content
   *
   */
  private function _showDefault() {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'default');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'default');

    $sModel = $this->__autoload('Blogs', true);
    $oModel = & new $sModel($this->_aRequest, $this->_aSession);

		if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
			$this->oSmarty->assign('data', $oModel->getData());
			$this->oSmarty->assign('_title_', $this->getTitle());
		}

		$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
		$this->oSmarty->setCacheLifetime(60);
    $this->oSmarty->setTemplateDir($sTemplateDir);
		exit($this->oSmarty->display($sTemplateFile, UNIQUE_ID));
	}

  /**
   * Show media RSS template
   *
   * @access private
   * @return string HTML content
   *
   */
  private function _showMedia() {
    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'media');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'media');

    $sModel = $this->__autoload('Galleries', true);
    $oModel = & new $sModel($this->_aRequest, $this->_aSession);
    $aData = $oModel->getData($this->_iId, false, true);

    if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
      $aData = & $aData[$this->_iId]['files'];
      rsort($aData);

      $this->oSmarty->assign('_copyright_', $aData[$this->_iId]['full_name']);
      $this->oSmarty->assign('_content_', $aData[$this->_iId]['content']);
      $this->oSmarty->assign('_locale_', WEBSITE_LOCALE);
      $this->oSmarty->assign('_link_', Helper::removeSlash($aData[$this->_iId]['url']));
      $this->oSmarty->assign('_pubdate_', $aData[$this->_iId]['datetime_rss']);
      $this->oSmarty->assign('_title_', $this->getTitle());

      $this->oSmarty->assign('data', $aData);
    }

    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_CURRENT);
    $this->oSmarty->setCacheLifetime(60);
    $this->oSmarty->setTemplateDir($sTemplateDir);
		exit($this->oSmarty->display($sTemplateFile, UNIQUE_ID));
  }
}
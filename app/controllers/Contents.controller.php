<?php

/**
 * CRUD action of content entries.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use Smarty;

class Contents extends Main {

  /**
   * Show content entry or content overview (depends on a given ID or not).
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    if (empty($this->_iId)) {
      $this->setTitle(I18n::get('global.manager.content'));

      $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'overview');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'overview');

      $this->oSmarty->setTemplateDir($sTemplateDir);
      $this->oSmarty->assign('contents', $this->_oModel->getData($this->_iId));

      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
    else {
      $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

			$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
			$this->oSmarty->setTemplateDir($sTemplateDir);

			if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
				$aData = & $this->_oModel->getData($this->_iId);
				$this->oSmarty->assign('content', $aData);

				if (!empty($aData)) {
					$this->setDescription($aData[$this->_iId]['teaser']);
					$this->setKeywords($aData[$this->_iId]['keywords']);
					$this->setTitle($this->_removeHighlight($aData[$this->_iId]['title']));
				}
				else {
          header('Status: 404 Not Found');
          header('HTTP/1.0 404 Not Found');
					Helper::redirectTo('/errors/404');
        }
			}

			return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
		}
  }

  /**
   * Build form template to create or update a content entry.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormTemplate() {
    if (!empty($this->_iId)) {
      $aData = $this->_oModel->getData($this->_iId, true);
      $this->setTitle($aData['title']);
    }
    else {
      $aData['title']			= isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
      $aData['teaser']		= isset($this->_aRequest['teaser']) ? $this->_aRequest['teaser'] : '';
      $aData['keywords']	= isset($this->_aRequest['keywords']) ? $this->_aRequest['keywords'] : '';
      $aData['content']		= isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
      $aData['published']	= isset($this->_aRequest['published']) ? $this->_aRequest['published'] : '';
    }

    foreach($aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir		= Helper::getTemplateDir($this->_aRequest['controller'], '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }
}
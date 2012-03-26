<?php

/**
 * CRUD action of blog entries.
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

class Blogs extends Main {

  /**
   * Show blog entry or blog overview (depends on a given ID or not).
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _show() {
    $sTemplateDir   = Helper::getTemplateDir($this->_aRequest['controller'], 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

    if ($this->_iId) {
      $this->_aData = $this->_oModel->getData($this->_iId);

      if (!$this->_aData[1]['id'])
        Helper::redirectTo('/errors/404');

      $sClass = $this->__autoload('Comments');
      $oComments = & new $sClass($this->_aRequest, $this->_aSession);
      $oComments->__init($this->_aData);

      $this->oSmarty->assign('blogs', $this->_aData);
      $this->oSmarty->assign('_blog_footer_', $oComments->show());
    }

    else {
      if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID)) {
        $this->_aData = $this->_oModel->getData();

        $this->oSmarty->assign('blogs', $this->_aData);
        $this->oSmarty->assign('_blog_footer_', $this->_oModel->oPagination->showSurrounding());
      }
    }

    $this->setDescription($this->_setBlogsDescription());
    $this->setKeywords($this->_setBlogsKeywords());
    $this->setTitle($this->_setBlogsTitle());

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Return the blog meta description and remove highlighted text if needed.
   *
   * @access private
   * @return string meta description
   *
   */
  private function _setBlogsDescription() {
    if (isset($this->_aRequest['page']) && $this->_aRequest['page'] > 1)
      return I18n::get('global.blogs') . ' - ' . I18n::get('global.page') . ' ' . (int) $this->_aRequest['page'];

    elseif ($this->_iId) {
      if (isset($this->_aData[1]['teaser']) && $this->_aData[1]['teaser'])
        return $this->_removeHighlight($this->_aData[1]['teaser']);

      elseif (isset($this->_aData[1]['title']))
        return $this->_removeHighlight($this->_aData[1]['title']);

      else
        return $this->_setBlogsTitle();
    }
    else
      return I18n::get('global.blogs');
  }

  /**
   * Return the blog meta keywords if they are set.
   *
   * @access private
   * @return string meta keywords
   *
   */
  private function _setBlogsKeywords() {
    if ($this->_iId && isset($this->_aData[1]['tags']) && !empty($this->_aData[1]['tags']))
      return $this->_aData[1]['keywords'];
  }

  /**
   * Return the blog title.
   *
   * @access private
   * @return string title
   *
   */
  private function _setBlogsTitle() {
    # Create blog entry.
    if (isset($this->_aRequest['action']) && 'create' == $this->_aRequest['action'])
      return $this->_aRequest['title'];

    # Show overview by blog tag
    elseif (isset($this->_aRequest['search']) && $this->_aRequest['search'] !== 'page')
      return I18n::get('global.tag') . ': ' . $this->_aRequest['search'];

    # default blog entry
    elseif ($this->_iId)
      return $this->_removeHighlight($this->_aData[1]['title']) . ' - ' . I18n::get('global.blogs');

    # show overview with pages
    else {
      $iPage = isset($this->_aRequest['page']) ? (int) $this->_aRequest['page'] : 1;
      return $iPage > 1 ?
              I18n::get('global.blog') . ' - ' . I18n::get('global.page') . ' ' . $iPage :
              I18n::get('global.blog');
    }
  }

  /**
   * Build form template to create or update a blog entry.
   *
   * @access protected
   * @return string HTML content
   *
   */
  protected function _showFormTemplate() {
    $sTemplateDir = Helper::getTemplateDir($this->_aRequest['controller'], '_form');
    $sTemplateFile = Helper::getTemplateType($sTemplateDir, '_form');

    # Update
    if ($this->_iId) {
      $aData = $this->_oModel->getData($this->_iId, true);
      $this->setTitle($aData['title']);
    }

    # Create
    else {
      $aData['content'] = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
      $aData['keywords'] = isset($this->_aRequest['keywords']) ? $this->_aRequest['keywords'] : '';
      $aData['language'] = isset($this->_aRequest['language']) ? $this->_aRequest['language'] : '';
      $aData['published'] = isset($this->_aRequest['published']) ? $this->_aRequest['published'] : '';
      $aData['tags'] = isset($this->_aRequest['tags']) ? $this->_aRequest['tags'] : '';
      $aData['teaser'] = isset($this->_aRequest['teaser']) ? $this->_aRequest['teaser'] : '';
      $aData['title'] = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
    }

    $this->oSmarty->assign('_tags_', $this->_oModel->getTypeaheadData('blogs', 'tags', true));

    # Get available languages
    $aData['languages'] = array();
    $oPathDir = opendir(PATH_STANDARD . '/languages');
    while ($sFile = readdir($oPathDir)) {
      if (substr($sFile, 0, 1) == '.' || substr($sFile, 0, 3) == 'de_')
        continue;

      array_push($aData['languages'], substr($sFile, 0, 2));
    }
    closedir($oPathDir);

    foreach ($aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Create a blog entry.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create() {
    $this->_setError('content');

    return parent::_create(array('searches', 'rss'));
  }

  /**
   * Update a blog entry.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _update() {
    $this->_setError('content');

    return parent::_update(array('searches', 'rss'));
  }

  /**
   * Destroy a blog entry.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    return parent::_destroy(array('searches', 'rss'));
  }
}

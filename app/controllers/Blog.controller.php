<?php

/**
 * CRUD action of blog entries.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Blog as Model;

require_once 'app/models/Blog.model.php';

class Blog extends Main {

  /**
   * @var object
   * @access public
   */
  public $oPage;

  /**
   * Include the blog model.
   *
   * @access public
   * @override app/controllers/Main.controller.php
   *
   */
  public function __init() {
    $this->_oModel = new Model($this->_aRequest, $this->_aSession);
  }

  /**
   * Show blog entry or blog overview (depends on a given ID or not).
   *
   * @access public
   * @return string HTML content
   *
   */
  public function show() {
    $this->__autoload('Comment');
    $this->_aData = $this->_oModel->getData($this->_iId);

    # Load comments
    if (!empty($this->_iId)) {
      $oComments = new Comment($this->_aRequest, $this->_aSession);
      $oComments->__init($this->_aData);

      $this->oSmarty->assign('_blog_footer_', $oComments->show());

    # Load blog pages
    } else
      $this->oSmarty->assign('_blog_footer_', $this->_oModel->oPage->showSurrounding('blog'));

    # Create page title and description
    $this->_setDescription($this->_setBlogDescription());
    $this->_setKeywords($this->_setBlogKeywords());
    $this->_setTitle($this->_setBlogTitle());

    $this->oSmarty->assign('blog', $this->_aData);

    $sTemplateDir = Helper::getTemplateDir('blogs', 'show');
    $this->oSmarty->template_dir = $sTemplateDir;
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'show'));
  }

  /**
   * Return the blog meta description and remove highlighted text if needed.
   *
   * @access private
   * @return string meta description
   *
   */
  private function _setBlogDescription() {
    if (isset($this->_aRequest['search']))
      return Helper::removeSlahes($this->_aRequest['search']); # Term that is being searched

    elseif (!empty($this->_iId)) {
      if (isset($this->_aData[1]['teaser']) && !empty($this->_aData[1]['teaser']))
        return $this->_removeHighlight($this->_aData[1]['teaser']);

      elseif (isset($this->_aData[1]['title']))
        return $this->_removeHighlight($this->_aData[1]['title']);

      else
        return $this->_setBlogTitle();

    } else
      return $this->oI18n->get('global.blog');
  }

  /**
   * Return the blog meta keywords if they are set.
   *
   * @access private
   * @return string meta keywords
   *
   */
  private function _setBlogKeywords() {
    if (!empty($this->_iId) && isset($this->_aData[1]['tags']) && !empty($this->_aData[1]['tags']))
      return $this->_aData[1]['keywords'];
  }

  /**
   * Return the blog title.
   *
   * @access private
   * @return string title
   *
   */
  private function _setBlogTitle() {
    # Create blog entry.
    if (isset($this->_aRequest['action']) &&
            'create' == $this->_aRequest['action'] &&
            'blog' == $this->_aRequest['section'])
      return Helper::removeSlahes($this->_aRequest['title']);

    # Show overview by blog tag
    elseif (isset($this->_aRequest['action']) && 'search' == $this->_aRequest['action'])
      return Helper::removeSlahes($this->_aRequest['id']);

    # default blog entry
    elseif (!empty($this->_iId))
      return $this->_removeHighlight($this->_aData[1]['title']);

    # show overview with pages
    else {
      $iPage = isset($this->_aRequest['page']) ? (int) $this->_aRequest['page'] : 1;
			return $iPage > 1 ?
							$this->oI18n->get('global.blog') . ' - ' . $this->oI18n->get('global.page') . ' ' . $iPage :
							$this->oI18n->get('global.blog');
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
    # Update
    if (!empty($this->_iId)) {
      $this->_aData = $this->_oModel->getData($this->_iId, true);
      $this->_setTitle(Helper::removeSlahes($this->_aData['title']));
    }
    # Create
    else {
      $this->_aData['content']    = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
      $this->_aData['keywords']   = isset($this->_aRequest['keywords']) ? $this->_aRequest['keywords'] : '';
      $this->_aData['published']  = isset($this->_aRequest['published']) ? $this->_aRequest['published'] : '';
      $this->_aData['tags']       = isset($this->_aRequest['tags']) ? $this->_aRequest['tags'] : '';
      $this->_aData['teaser']     = isset($this->_aRequest['teaser']) ? $this->_aRequest['teaser'] : '';
      $this->_aData['title']      = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
    }

    foreach ($this->_aData as $sColumn => $sData)
      $this->oSmarty->assign($sColumn, $sData);

    if (!empty($this->_aError))
      $this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir = Helper::getTemplateDir('blogs', '_form');
    $this->oSmarty->template_dir = $sTemplateDir;
    return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, '_form'));
  }

  /**
   * Create a blog entry.
   *
   * Check if required data is given or throw an error instead.
   * If data is given, activate the model, insert them into the database and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create() {
    $this->_setError('title');
    $this->_setError('content');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->create() === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('blogs'));
      return Helper::successMessage($this->oI18n->get('success.create'), '/blog');
    }
    else
      return Helper::errorMessage($this->oI18n->get('error.sql.query'), '/blog');
  }

  /**
   * Update a blog entry.
   *
   * Activate model, insert data into the database and redirect afterwards.
   *
   * @access protected
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _update() {
    $this->_setError('title');

    if (isset($this->_aError))
      return $this->_showFormTemplate();

    elseif ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id']);
      return Helper::successMessage($this->oI18n->get('success.update'), '/blog/' . (int) $this->_aRequest['id']);
    }
    else
      return Helper::errorMessage($this->oI18n->get('error.sql.query'), '/blog');
  }

  /**
   * Delete a blog entry.
   *
   * Activate model, delete data from database and redirect afterwards.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id']);
      return Helper::successMessage($this->oI18n->get('success.destroy'), '/blog');
    }
    else
      return Helper::errorMessage($this->oI18n->get('error.sql.query'), '/blog');
  }
}
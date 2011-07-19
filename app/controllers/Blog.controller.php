<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Blog.model.php';
require_once 'app/helpers/Page.helper.php';
require_once 'app/controllers/Comment.controller.php';

class Blog extends Main {
	public $oPage;

	public function __init() {
		$this->_oModel = new Model_Blog($this->_aRequest, $this->_aSession);
	}

	public function show() {
    $this->_aData = $this->_oModel->getData($this->_iId);

    # Load comments
    if( !empty($this->_iId) ) {
      $oComments = new Comment($this->_aRequest, $this->_aSession);
      $oComments->__init($this->_aData);

      $this->_oSmarty->assign('_blog_footer_', $oComments->show());

    # Load blog pages
    } else
      $this->_oSmarty->assign('_blog_footer_', $this->_oModel->oPage->showSurrounding('/blog', 'blog'));

		# Create page title and description
    $this->_setDescription($this->_setBlogDescription());
    $this->_setKeywords($this->_setBlogKeywords());
		$this->_setTitle($this->_setBlogTitle($this->_aData));

		$this->_oSmarty->assign('blog', $this->_aData);

		$this->_oSmarty->template_dir = Helper::getTemplateDir('blogs/show');
		return $this->_oSmarty->fetch('blogs/show.tpl');
	}

  # Quick hack for displaying title without html tags
  private function _removeHighlight($sTitle) {
    $sTitle = Helper::removeSlahes($sTitle);
    $sTitle = str_replace('<mark>', '', $sTitle);
    $sTitle = str_replace('</mark>', '', $sTitle);
    return $sTitle;
  }

  private function _setBlogDescription() {
    if (isset($this->_aRequest['action']) &&
            'search' == $this->_aRequest['action'])
      return Helper::removeSlahes($this->_aRequest['id']);

    elseif (!empty($this->_iId)) {
      if (isset($this->_aData[1]['teaser']) && !empty($this->_aData[1]['teaser']))
        return $this->_removeHighlight($this->_aData[1]['teaser']);

      elseif (isset($this->_aData[1]['title']))
        return $this->_removeHighlight($this->_aData[1]['title']);

      else
        return $this->_setBlogTitle();

    } else
      return LANG_GLOBAL_BLOG;
  }

  private function _setBlogKeywords() {
    if (!empty($this->_iId) && isset($this->_aData[1]['tags']) && !empty($this->_aData[1]['tags']))
      return $this->_aData[1]['keywords'];
  }

	private function _setBlogTitle($aData) {
    # Create blog
    if (isset($this->_aRequest['action']) &&
            'create' == $this->_aRequest['action'] &&
            'blog' == $this->_aRequest['section'])
      return Helper::removeSlahes($this->_aRequest['title']);

    # Tags
    elseif (isset($this->_aRequest['action']) &&
            'search' == $this->_aRequest['action'])
      return Helper::removeSlahes($this->_aRequest['id']);

    # Default blog
    elseif (!empty($this->_iId))
      return $this->_removeHighlight($this->_aData[1]['title']);

		# Blog overview with pages
		else {
			$iPage = isset($this->_aRequest['page']) ? (int) $this->_aRequest['page'] : 1;

			if ($iPage > 1)
				return LANG_GLOBAL_BLOG . ' - ' . LANG_GLOBAL_PAGE . ' ' . $iPage;
			else
				return LANG_GLOBAL_BLOG;
		}
	}

	protected function _showFormTemplate($bUpdate = true) {

		# Show update template
		if ($bUpdate == true) {
			$this->_aData = $this->_oModel->getData($this->_iId, true);
			$this->_oSmarty->assign('_action_url_', '/blog/update');
			$this->_oSmarty->assign('_formdata_', 'update_blog');
			$this->_oSmarty->assign('author_id', $this->_aData['author_id']);
			$this->_oSmarty->assign('content', $this->_aData['content']);
			$this->_oSmarty->assign('keywords', $this->_aData['keywords']);
			$this->_oSmarty->assign('published', $this->_aData['published']);
			$this->_oSmarty->assign('tags', $this->_aData['tags']);
			$this->_oSmarty->assign('teaser', $this->_aData['teaser']);
			$this->_oSmarty->assign('title', $this->_aData['title']);

			# Build up title
			$this->_setTitle(Helper::removeSlahes($this->_aData['title']));

			$this->_oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
			$this->_oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);
		}
		# Create blog
		else {
			$sContent		= isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
			$iKeywords  = isset($this->_aRequest['keywords']) ? $this->_aRequest['keywords'] : '';
			$iPublished = isset($this->_aRequest['published']) ? $this->_aRequest['published'] : '';
			$sTags			= isset($this->_aRequest['tags']) ? $this->_aRequest['tags'] : '';
			$sTeaser		= isset($this->_aRequest['teaser']) ? $this->_aRequest['teaser'] : '';
			$sTitle			= isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';

			$this->_oSmarty->assign('_action_url_', '/blog/create');
			$this->_oSmarty->assign('_formdata_', 'create_blog');
			$this->_oSmarty->assign('_request_id_', '');
			$this->_oSmarty->assign('content', $sContent);
			$this->_oSmarty->assign('keywords', $iKeywords);
			$this->_oSmarty->assign('published', $iPublished);
			$this->_oSmarty->assign('tags', $sTags);
			$this->_oSmarty->assign('teaser', $sTeaser);
			$this->_oSmarty->assign('title', $sTitle);

			$this->_oSmarty->assign('lang_headline', LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
			$this->_oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);
		}

		if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->_oSmarty->assign('error_' . $sField, $sMessage);
		}

		$this->_oSmarty->assign('lang_create_keywords_info', LANG_BLOG_INFO_KEYWORDS);
		$this->_oSmarty->assign('lang_create_tag_info', LANG_BLOG_INFO_TAG);
		$this->_oSmarty->assign('lang_create_teaser_info', LANG_BLOG_INFO_TEASER);

		$this->_oSmarty->template_dir = Helper::getTemplateDir('blogs/_form');
		return $this->_oSmarty->fetch('blogs/_form.tpl');
	}

	protected function _create() {
		if (!isset($this->_aRequest['title']) || empty($this->_aRequest['title']))
			$this->_aError['title'] = LANG_ERROR_FORM_MISSING_TITLE;

		if (isset($this->_aError))
			return $this->_showFormTemplate(false);

		elseif ($this->_oModel->create() === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('blogs'));
			return Helper::successMessage(LANG_SUCCESS_CREATE, '/blog');
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/blog');
	}

	protected function _update() {
		if ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'],  (int) $this->_aRequest['id']);
			return Helper::successMessage(LANG_SUCCESS_UPDATE, '/blog/' . (int) $this->_aRequest['id']);
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/blog');
	}

	protected function _destroy() {
		if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
      Log::insert($this->_aRequest['section'], $this->_aRequest['action'],  (int) $this->_aRequest['id']);
			return Helper::successMessage(LANG_SUCCESS_DESTROY, '/blog');
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/blog');
	}
}
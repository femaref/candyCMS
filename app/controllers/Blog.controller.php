<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Blog.model.php';
require_once 'app/helpers/Pages.helper.php';
require_once 'app/controllers/Comment.controller.php';

class Blog extends Main {
	public $oPages;

	public function __init() {
		$this->_oModel = new Model_Blog($this->_aRequest, $this->_aSession);
	}

	public function show() {
		$this->_aData = $this->_oModel->getData($this->_iId);
		$this->_oSmarty->assign('blog', $this->_aData);

		# Count comments
		$iCommentSum = 0;
		if (!empty($this->_iId))
			$iCommentSum = $this->_aData[1]['comment_sum'];

		# Load Comments
		$oComments = new Comment($this->_aRequest, $this->_aSession);
		$oComments->__init($iCommentSum, $this->_aData);

		$this->_oSmarty->assign('_blog_comments_', $oComments->show());
		$this->_oSmarty->assign('_blog_pages_', $this->_oModel->oPages->showSurrounding('Blog', 'blog'));

		# Create page title
		$this->_setTitle($this->_setBlogTitle($this->_aData));

		$this->_oSmarty->template_dir = Helper::getTemplateDir('blogs/show');
		return $this->_oSmarty->fetch('blogs/show.tpl');
	}

	private final function _setBlogTitle($aData) {
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
		elseif ($this->_iId !== '') {
			# Quick hack for displaying title without html tags
			$sTitle = Helper::removeSlahes($this->_aData[1]['title']);
			$sTitle = str_replace('<span class="highlight">', '', $sTitle);
			$sTitle = str_replace('</span>', '', $sTitle);
			return $sTitle;
		}

		# Blog overview with pages
		else {
			$iPage = isset($this->_aRequest['page']) ? (int) $this->_aRequest['page'] : 1;

			if ($iPage > 1)
				return LANG_GLOBAL_BLOG . ' - ' . LANG_GLOBAL_PAGE . ' ' . $iPage;
			else
				return LANG_GLOBAL_BLOG;
		}
	}

	protected final function _showFormTemplate($bUpdate = true) {

		# Show update template
		if ($bUpdate == true) {
			$this->_aData = $this->_oModel->getData($this->_iId, true);
			$this->_oSmarty->assign('_action_url_', '/Blog/update');
			$this->_oSmarty->assign('_formdata_', 'update_blog');
			$this->_oSmarty->assign('author_id', $this->_aData['author_id']);
			$this->_oSmarty->assign('tags', $this->_aData['tags']);
			$this->_oSmarty->assign('title', $this->_aData['title']);
			$this->_oSmarty->assign('teaser', $this->_aData['teaser']);
			$this->_oSmarty->assign('content', $this->_aData['content']);
			$this->_oSmarty->assign('published', $this->_aData['published']);

			# Build up title
			$this->_setTitle(Helper::removeSlahes($this->_aData['title']));

			$this->_oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
			$this->_oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);
		}
		# Create blog
		else {
			$sTitle			= isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
			$sTags			= isset($this->_aRequest['tags']) ? $this->_aRequest['tags'] : '';
			$sTeaser		= isset($this->_aRequest['teaser']) ? $this->_aRequest['teaser'] : '';
			$sContent		= isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
			$iPublished = isset($this->_aRequest['published']) ? $this->_aRequest['published'] : '';

			$this->_oSmarty->assign('_action_url_', '/Blog/create');
			$this->_oSmarty->assign('_formdata_', 'create_blog');
			$this->_oSmarty->assign('_request_id_', '');
			$this->_oSmarty->assign('title', $sTitle);
			$this->_oSmarty->assign('tags', $sTags);
			$this->_oSmarty->assign('content', $sContent);
			$this->_oSmarty->assign('published', $iPublished);

			$this->_oSmarty->assign('lang_headline', LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
			$this->_oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);
		}

		if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$this->_oSmarty->assign('error_' . $sField, $sMessage);
		}

		$this->_oSmarty->assign('lang_create_tag_info', LANG_BLOG_INFO_TAG);
		$this->_oSmarty->assign('lang_create_teaser_info', LANG_BLOG_INFO_TEASER);

		$this->_oSmarty->template_dir = Helper::getTemplateDir('blogs/_form');
		return $this->_oSmarty->fetch('blogs/_form.tpl');
	}

	protected final function _create() {
		if (!isset($this->_aRequest['title']) || empty($this->_aRequest['title']))
			$this->_aError['title'] = LANG_ERROR_FORM_MISSING_TITLE;

		if (isset($this->_aError))
			return $this->_showFormTemplate(false);

		elseif ($this->_oModel->create() === true) {
      Helper::log($this->_aRequest['section'], $this->_aRequest['action'], Helper::getLastEntry('blogs'));
			return Helper::successMessage(LANG_SUCCESS_CREATE, '/Blog');
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/Blog');
	}

	protected final function _update() {
		if ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
      Helper::log($this->_aRequest['section'], $this->_aRequest['action'],  (int) $this->_aRequest['id']);
			return Helper::successMessage(LANG_SUCCESS_UPDATE, '/Blog/' . (int) $this->_aRequest['id']);
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/Blog');
	}

	protected function _destroy() {
		if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
      Helper::log($this->_aRequest['section'], $this->_aRequest['action'],  (int) $this->_aRequest['id']);
			return Helper::successMessage(LANG_SUCCESS_DESTROY, '/Blog');
    }
		else
			return Helper::errorMessage(LANG_ERROR_SQL_QUERY, '/Blog');
	}
}
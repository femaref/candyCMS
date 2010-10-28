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

		$oSmarty = new Smarty();
		$oSmarty->assign('blog', $this->_aData);
		$oSmarty->assign('USER_ID', USER_ID);
		$oSmarty->assign('USER_RIGHT', USER_RIGHT);
		$oSmarty->assign('URL', WEBSITE_URL);
		$oSmarty->assign('pid', $this->_iId);

		# Manage Comments
		$iCommentSum = 0;
		if (!empty($this->_iId))
			$iCommentSum = $this->_aData[1]['comment_sum'];

		# System variables
		$oComments = new Comment($this->_aRequest, $this->_aSession);
		$oComments->__init($iCommentSum, $this->_aData);
		$oSmarty->assign('_blog_comments_', $oComments->show());
		$oSmarty->assign('_blog_pages_', $this->_oModel->oPages->showSurrounding('Blog', 'blog'));

		# Language
		$oSmarty->assign('lang_add_bookmark', LANG_GLOBAL_ADD_BOOKMARK);
		$oSmarty->assign('lang_create_entry_headline', LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
		$oSmarty->assign('lang_by', LANG_GLOBAL_BY);
		$oSmarty->assign('lang_comments', LANG_GLOBAL_COMMENTS);
		$oSmarty->assign('lang_update', LANG_GLOBAL_UPDATE);
		$oSmarty->assign('lang_last_update', LANG_GLOBAL_LAST_UPDATE);
		$oSmarty->assign('lang_missing_entry', LANG_ERROR_GLOBAL_MISSING_ENTRY);
		$oSmarty->assign('lang_no_entries', LANG_ERROR_GLOBAL_NO_ENTRIES);
		$oSmarty->assign('lang_not_published', LANG_ERROR_GLOBAL_NOT_PUBLISHED);
		$oSmarty->assign('lang_share', LANG_GLOBAL_SHARE);
		$oSmarty->assign('lang_tags', LANG_GLOBAL_TAGS);
		$oSmarty->assign('lang_tags_info', LANG_GLOBAL_TAGS_INFO);

		# Create Page-Title
		$this->_setTitle($this->_setBlogTitle($this->_aData));

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
		$oSmarty->template_dir = Helper::getTemplateDir('blogs/show');
		return $oSmarty->fetch('blogs/show.tpl');
	}

	private final function _setBlogTitle($aData) {
		if (isset($this->_aRequest['action']) &&
						'create' == $this->_aRequest['action'] &&
						'blog' == $this->_aRequest['section'])
			return Helper::removeSlahes($this->_aRequest['title']);

		elseif (isset($this->_aRequest['action']) &&
						'search' == $this->_aRequest['action'])
			return Helper::removeSlahes($this->_aRequest['id']);

		elseif ($this->_iId !== '') {
			# Quick hack for displaying title without html tags
			$sTitle = Helper::removeSlahes($this->_aData[1]['title']);
			$sTitle = str_replace('<span class="highlight">', '', $sTitle);
			$sTitle = str_replace('</span>', '', $sTitle);
			return $sTitle;
		}
		else {
			$iPage = isset($this->_aRequest['page']) ?
							(int) $this->_aRequest['page'] :
							1;

			if ($iPage > 1)
				return LANG_GLOBAL_BLOG . ' - ' . LANG_GLOBAL_PAGE . ' ' . $iPage;
			else
				return LANG_GLOBAL_BLOG;
		}
	}

	protected final function _showFormTemplate($bUpdate = true) {
		$oSmarty = new Smarty();

		# Show Update Template
		if ($bUpdate == true) {
			# collect data array
			$this->_aData = $this->_oModel->getData($this->_iId, true);
			$oSmarty->assign('_action_url_', '/Blog/update');
			$oSmarty->assign('_formdata_', 'update_blog');
			$oSmarty->assign('id', $this->_iId);
			$oSmarty->assign('author_id', $this->_aData['author_id']);
			$oSmarty->assign('tags', $this->_aData['tags']);
			$oSmarty->assign('title', $this->_aData['title']);
			$oSmarty->assign('teaser', $this->_aData['teaser']);
			$oSmarty->assign('content', $this->_aData['content']);
			$oSmarty->assign('published', $this->_aData['published']);

			# Build up title
			$this->_setTitle(Helper::removeSlahes($this->_aData['title']));

			# Language
			$oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
			$oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
			$oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);
		}
		# Add Blog Template
		else {
			$sTitle = isset($this->_aRequest['title']) ?
							$this->_aRequest['title'] :
							'';

			$sTags = isset($this->_aRequest['tags']) ?
							$this->_aRequest['tags'] :
							'';

			$sTeaser = isset($this->_aRequest['teaser']) ?
							$this->_aRequest['teaser'] :
							'';

			$sContent = isset($this->_aRequest['content']) ?
							$this->_aRequest['content'] :
							'';

			$iPublished = isset($this->_aRequest['published']) ?
							$this->_aRequest['published'] :
							'';

			$oSmarty->assign('_action_url_', '/Blog/create');
			$oSmarty->assign('_formdata_', 'create_blog');
			$oSmarty->assign('id', '');
			$oSmarty->assign('title', $sTitle);
			$oSmarty->assign('tags', $sTags);
			$oSmarty->assign('content', $sContent);
			$oSmarty->assign('published', $iPublished);

			# Language
			$oSmarty->assign('lang_headline', LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
			$oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);
		}

		if (!empty($this->_aError)) {
			foreach ($this->_aError as $sField => $sMessage)
				$oSmarty->assign('error_' . $sField, $sMessage);
		}

    $oSmarty->assign('_language_', substr(DEFAULT_LANGUAGE, 0, 2));

		# More language
		$oSmarty->assign('lang_bb_help', LANG_GLOBAL_BBCODE_HELP);
		$oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
		$oSmarty->assign('lang_create_tag_info', LANG_BLOG_INFO_TAG);
		$oSmarty->assign('lang_create_teaser_info', LANG_BLOG_INFO_TEASER);
		$oSmarty->assign('lang_currently', LANG_GLOBAL_CURRENTLY);
		$oSmarty->assign('lang_destroy_entry', LANG_GLOBAL_DESTROY_ENTRY);
		$oSmarty->assign('lang_published', LANG_GLOBAL_PUBLISHED);
		$oSmarty->assign('lang_tags', LANG_GLOBAL_TAGS);
		$oSmarty->assign('lang_teaser', LANG_GLOBAL_TEASER);
		$oSmarty->assign('lang_title', LANG_GLOBAL_TITLE);
		$oSmarty->assign('lang_update_show', LANG_GLOBAL_UPDATE_SHOW);

    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;
		$oSmarty->template_dir = Helper::getTemplateDir('blogs/_form');
		return $oSmarty->fetch('blogs/_form.tpl');
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
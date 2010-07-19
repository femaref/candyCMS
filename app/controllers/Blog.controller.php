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
	private $_oPages;

	public function __init() {
		$this->_oModel = new Model_Blog($this->m_aRequest, $this->m_oSession);
	}

	public function show() {
		$this->_aData = $this->_oModel->getData($this->_iID);

		$oSmarty = new Smarty();
		$oSmarty->assign('blog', $this->_aData);
		$oSmarty->assign('dev', WEBSITE_DEV);
		$oSmarty->assign('uid', USERID);
		$oSmarty->assign('UR', USERRIGHT);
		$oSmarty->assign('URL', WEBSITE_URL);
		$oSmarty->assign('pid', $this->_iID);

		# Extra included stuff
		$oSmarty->assign('blogPages', $this->_oModel->_oPages->showSurrounding('Blog', 'blog'));

		# Manage Comments
		$iCommentSum = 0;
		if(!empty($this->_iID))
			$iCommentSum = $this->_aData[1]['comment_sum'];
		$oComments = new Comment($this->m_aRequest, $this->m_oSession);
		$oComments->__init($iCommentSum, $this->_aData);
		$oSmarty->assign('blogComments', $oComments->show());

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
		$this->_setTitle( $this->_setBlogTitle($this->_aData) );

		$oSmarty->template_dir = Helper::templateDir('blog/show');
		return $oSmarty->fetch('blog/show.tpl');
	}

	private final function _setBlogTitle($aData) {
		if( isset($this->m_aRequest['action']) &&
				'create'	== $this->m_aRequest['action'] &&
				'blog'		== $this->m_aRequest['section'])
			return Helper::removeSlahes($this->m_aRequest['title']);

		elseif( isset($this->m_aRequest['action']) &&
				'tag' == $this->m_aRequest['action'])
			return Helper::removeSlahes(LANG_GLOBAL_TAGS.	': '
					.$this->m_aRequest['id']);

		elseif( $this->_iID !== '' )
			return Helper::removeSlahes($this->_aData[1]['title']);

		else {
			$iPage = isset($this->m_aRequest['page']) ?
					(int)$this->m_aRequest['page'] :
					1;

			if( $iPage > 1)
				return LANG_GLOBAL_BLOG.	' - '
						.LANG_GLOBAL_PAGE.	' '	.$iPage;
			else
				return LANG_GLOBAL_BLOG;
		}
	}

	protected final function _showFormTemplate($bUpdate = true) {
		$oSmarty = new Smarty();

		# Show Update Template
		if($bUpdate == true) {
			# collect data array
			$this->_aData = $this->_oModel->getData($this->_iID, true);
			$oSmarty->assign('id', $this->_iID);
			$oSmarty->assign('tags', $this->_aData['tags']);
			$oSmarty->assign('title', $this->_aData['title']);
			$oSmarty->assign('content', $this->_aData['content']);
			$oSmarty->assign('published', $this->_aData['published']);
			$oSmarty->assign('action', '/Blog/update');
			$oSmarty->assign('formdata', 'update_blog');

			# Build up title
			$this->_setTitle(Helper::removeSlahes($this->_aData['title']));

			# Language
			$oSmarty->assign('lang_headline', LANG_GLOBAL_UPDATE_ENTRY);
			$oSmarty->assign('lang_reset', LANG_GLOBAL_RESET);
			$oSmarty->assign('lang_submit', LANG_GLOBAL_UPDATE_ENTRY);
		}
		# Add Blog Template
		else {
			$sTitle = isset($this->m_aRequest['title']) ?
					$this->m_aRequest['title'] :
					'';

			$sTags = isset($this->m_aRequest['tags']) ?
					$this->m_aRequest['tags'] :
					'';

			$sContent = isset($this->m_aRequest['content']) ?
					$this->m_aRequest['content'] :
					'';

			$iPublished = isset($this->m_aRequest['published']) ?
					$this->m_aRequest['published'] :
					'';

			$oSmarty->assign('id', '');
			$oSmarty->assign('title', $sTitle);
			$oSmarty->assign('tags', $sTags);
			$oSmarty->assign('content', $sContent);
			$oSmarty->assign('published', $iPublished);
			$oSmarty->assign('action', '/Blog/create');
			$oSmarty->assign('formdata', 'create_blog');

			# Build up title
			$this->_setTitle(LANG_BLOG_CREATE);

			# Language
			$oSmarty->assign('lang_headline', LANG_GLOBAL_CREATE_ENTRY_HEADLINE);
			$oSmarty->assign('lang_submit', LANG_GLOBAL_CREATE_ENTRY);
		}

		# More language
		$oSmarty->assign('lang_bb_help', LANG_GLOBAL_BBCODE_HELP);
		$oSmarty->assign('lang_content', LANG_GLOBAL_CONTENT);
		$oSmarty->assign('lang_create_tag_info', LANG_BLOG_CREATE_TAG_INFO);
		$oSmarty->assign('lang_currently', LANG_GLOBAL_CURRENTLY);
		$oSmarty->assign('lang_destroy_entry', LANG_GLOBAL_DESTROY_ENTRY);
		$oSmarty->assign('lang_published', LANG_GLOBAL_PUBLISHED);
		$oSmarty->assign('lang_tags', LANG_GLOBAL_TAGS);
		$oSmarty->assign('lang_title', LANG_GLOBAL_TITLE);
		$oSmarty->assign('lang_update_show', LANG_BLOG_UPDATE_SHOW);

		$oSmarty->template_dir = Helper::templateDir('blog/_form');
		return $oSmarty->fetch('blog/_form.tpl');
	}

	protected final function _create() {
		$sError = '';

		if(	!isset($this->m_aRequest['title']) ||
				empty($this->m_aRequest['title']) )
			$sError .= LANG_GLOBAL_TITLE.	'<br />';

		if(	!isset($this->m_aRequest['content']) ||
				empty($this->m_aRequest['content']) )
			$sError .= LANG_GLOBAL_CONTENT.	'<br />';

		if( !empty($sError) ) {
			$sReturn  = Helper::errorMessage($sError, LANG_ERROR_GLOBAL_CHECK_FIELDS);
			$sReturn .= $this->_showFormTemplate(false);
			return $sReturn;
		}
		elseif( $this->_oModel->create() == true )
			return Helper::successMessage(LANG_SUCCESS_CREATE).
					$this->show();
		else
			return Helper::errorMessage($sError, LANG_ERROR_DB_QUERY);
	}

	protected final function _update() {
		if( $this->_oModel->update((int)$this->m_aRequest['id']) == true)
			return Helper::successMessage(LANG_SUCCESS_UPDATE).
					$this->show();
		else
			return Helper::errorMessage(LANG_ERROR_DB_QUERY);
	}

	protected function _destroy() {
		if( $this->_oModel->destroy((int)$this->m_aRequest['id']))
			return Helper::successMessage(LANG_SUCCESS_UPDATE).
					$this->show();
		else
			return Helper::errorMessage(LANG_ERROR_DB_QUERY);
	}
}
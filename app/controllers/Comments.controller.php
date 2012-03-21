<?php

/**
 * CRD action of comments.
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
use CandyCMS\Plugin\Controller\Recaptcha as Recaptcha;

class Comments extends Main {

  /**
   * Include the content model.
   *
   * @access public
   * @param array $aParentData optionally provided blog data
   *
   */
  public function __init($aParentData = '') {
    $oModel = $this->__autoload('Comments', true);
    $this->_oModel = & new $oModel($this->_aRequest, $this->_aSession);

		$this->_aParentData = & $aParentData;
	}

  /**
   * Show comment entries.
   *
   * @access protected
   * @return string HTML content
   * @todo isCached?!
   *
   */
  protected function _show() {
		$sTemplateDir		= Helper::getTemplateDir('comments', 'show');
		$sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

		$this->oSmarty->assign('comments',
						$this->_oModel->getData($this->_iId, (int) $this->_aParentData[1]['comment_sum'], LIMIT_COMMENTS));

		# Set author of blog entry
		$this->oSmarty->assign('author_id', (int) $this->_aParentData[1]['author_id']);

		# For correct information, do some math to display entries.
		# NOTE: If you're admin, you can see all entries. That might bring pagination to your view, even
		# when other people don't see it
		$this->oSmarty->assign('comment_number',
						($this->_oModel->oPagination->getCurrentPage() * LIMIT_COMMENTS) - LIMIT_COMMENTS);

		# Do we need pages?
		$this->oSmarty->assign('_pages_', $this->_oModel->oPagination->showPages('/blogs/' . $this->_iId));

		$this->oSmarty->setTemplateDir($sTemplateDir);

 		return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID) . $this->create('create_comments');
  }

  /**
   * Build form template to create a comment.
   *
   * @access protected
   * @param boolean $bShowCaptcha force captcha or not
   * @return string HTML content
   *
   */
  protected function _showFormTemplate($bShowCaptcha) {
    $sTemplateDir		= Helper::getTemplateDir('comments', '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

    $this->oSmarty->assign('content', isset($this->_aRequest['content']) ? (string) $this->_aRequest['content'] : '');
    $this->oSmarty->assign('email', isset($this->_aRequest['email']) ? (string) $this->_aRequest['email'] : '');
    $this->oSmarty->assign('name', isset($this->_aRequest['name']) ? (string) $this->_aRequest['name'] : '');

    if ($bShowCaptcha === true)
      $this->oSmarty->assign('_captcha_', Recaptcha::getInstance()->show());

    if ($this->_aError)
      $this->oSmarty->assign('error', $this->_aError);

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }

  /**
   * Create entry, check for captcha or show form template if we have enough roles.
   * We must override the main method due to a diffent required user role.
   *
   * @access public
   * @param string $sInputName sent input name to verify action
   * @return string HTML content
   *
   */
  public function create($sInputName) {
    $bShowCaptcha = class_exists('\CandyCMS\Plugin\Controller\Recaptcha') ?
                      $this->_aSession['user']['role'] == 0 && SHOW_CAPTCHA :
                      false;

    #no caching for comments
    $this->oSmarty->setCaching(false);

    if (isset($this->_aRequest[$sInputName]))
			return	$this->_create($bShowCaptcha);

		else
			return $this->_showFormTemplate($bShowCaptcha);
	}

  /**
   * Create a blog entry.
   *
   * Check if required data is given or throw an error instead.
   *
   * @access protected
   * @param boolean $bShowCaptcha show captcha?
   * @return string|boolean HTML content (string) or returned status of model action (boolean).
   *
   */
  protected function _create($bShowCaptcha = true) {
    $this->_setError('parent_id', I18n::get('error.missing.id'));
    $this->_setError('content');

    if ($this->_aSession['user']['role'] == 0)
      $this->_setError('name');

    if (isset($this->_aRequest['email']) && $this->_aRequest['email'])
      $this->_setError('email');

    if ($bShowCaptcha === true && Recaptcha::getInstance()->checkCaptcha($this->_aRequest) === false)
      $this->_aError['captcha'] = I18n::get('error.captcha.loading');

    if ($this->_aError)
      return $this->_showFormTemplate($bShowCaptcha);

    else {
      $sRedirect = '/blogs/' . (int) $this->_aRequest['parent_id'] . '#create';

      if ($this->_oModel->create() === true) {
        $this->oSmarty->clearCacheForController($this->_aRequest['controller']);

        Logs::insert('comments', 'create', Helper::getLastEntry('comments'), $this->_aSession['user']['id']);

        return Helper::successMessage(I18n::get('success.create'), $sRedirect);
      }
      else
        return Helper::errorMessage(I18n::get('error.sql'), $sRedirect);
    }
  }

  /**
   * Delete a a comment.
   *
   * @access protected
   * @return boolean status of model action
   *
   */
  protected function _destroy() {
    $sRedirect = '/blogs/' . $this->_oModel->getParentId((int) $this->_aRequest['id']);

    if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
      $this->oSmarty->clearCacheForController('blogs');

      Logs::insert(	'comment',
										'destroy',
										(int) $this->_aRequest['id'],
										$this->_aSession['user']['id']);

      return Helper::successMessage(I18n::get('success.destroy'), $sRedirect);
    }
    else
      return Helper::errorMessage(I18n::get('error.sql'), $sRedirect);
  }
}
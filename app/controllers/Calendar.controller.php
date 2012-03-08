<?php

/**
 * CRUD action of simple calendar.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Model\Calendar as Model;
use Smarty;

class Calendar extends Main {

  /**
	 * Include the calendar model.
	 *
	 * @access public
	 *
	 */
	public function __init() {
    require PATH_STANDARD . '/app/models/Calendar.model.php';
		$this->_oModel = new Model($this->_aRequest, $this->_aSession);
	}

	/**
	 * Show calendar overview.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
	protected function _show() {
    # Show .ics
    if (!empty($this->_iId) && !isset($this->_aRequest['action'])) {
			$this->oSmarty->assign('calendar', $this->_oModel->getData($this->_iId));

      header('Content-type: text/calendar; charset=utf-8');
      header('Content-Disposition: inline; filename=' . I18n::get('global.event') . '.ics');

      $this->oSmarty->setTemplateDir(Helper::getTemplateDir($this->_sTemplateFolder, 'ics'));
      return $this->oSmarty->fetch('ics.tpl', UNIQUE_ID);
    }

    # Show overview
    else {
      $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, 'show');
      $sTemplateFile	= Helper::getTemplateType($sTemplateDir, 'show');

			$this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
      $this->oSmarty->setTemplateDir($sTemplateDir);

			if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID))
				$this->oSmarty->assign('calendar', $this->_oModel->getData($this->_iId));

      return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
    }
	}

  /**
	 * Build form template to create or update a calendar entry.
	 *
	 * @access protected
	 * @return string HTML content
	 *
	 */
	protected function _showFormTemplate() {
		# Update
		if (!empty($this->_iId))
			$aData = & $this->_oModel->getData($this->_iId, true);

		# Create
		else {
			$aData['content']			= isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
			$aData['end_date']		= isset($this->_aRequest['end_date']) ? $this->_aRequest['end_date'] : '';
			$aData['start_date']	= isset($this->_aRequest['start_date']) ? $this->_aRequest['start_date'] : '';
			$aData['title']				= isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
		}

		foreach ($aData as $sColumn => $sData)
			$this->oSmarty->assign($sColumn, $sData);

		if (!empty($this->_aError))
			$this->oSmarty->assign('error', $this->_aError);

    $sTemplateDir		= Helper::getTemplateDir($this->_sTemplateFolder, '_form');
    $sTemplateFile	= Helper::getTemplateType($sTemplateDir, '_form');

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
	}

  /**
	 * Create a download entry.
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
		$this->_setError('start_date');

		if (isset($this->_aError))
			return $this->_showFormTemplate();

		elseif ($this->_oModel->create() === true) {
			$this->oSmarty->clearCache(null, $this->_aRequest['controller']);

			Log::insert($this->_aRequest['controller'],
									$this->_aRequest['action'],
									$this->_oModel->getLastInsertId('calendars'),
									$this->_aSession['userdata']['id']);

			return Helper::successMessage(I18n::get('success.create'), '/calendar');
		}
		else
			return Helper::errorMessage(I18n::get('error.sql'), '/calendar');
	}

	/**
	 * Update a calendar entry.
	 *
	 * Activate model, insert data into the database and redirect afterwards.
	 *
	 * @access protected
	 * @return boolean status of model action
	 *
	 */
	protected function _update() {
		$this->_setError('title');
		$this->_setError('start_date');

		if (isset($this->_aError))
			return $this->_showFormTemplate();

		elseif ($this->_oModel->update((int) $this->_aRequest['id']) === true) {
			$this->oSmarty->clearCache(null, $this->_aRequest['controller']);

			Log::insert($this->_aRequest['controller'],
									$this->_aRequest['action'],
									(int) $this->_aRequest['id'],
									$this->_aSession['userdata']['id']);

			return Helper::successMessage(I18n::get('success.update'), '/calendar');
		}
		else
			return Helper::errorMessage(I18n::get('error.sql'), '/calendar');
	}

	/**
	 * Delete a calendar entry.
	 *
	 * Activate model, delete data from database and redirect afterwards.
	 *
	 * @access protected
	 * @return boolean status of model action
	 *
	 */
	protected function _destroy() {
		if ($this->_oModel->destroy((int) $this->_aRequest['id']) === true) {
			$this->oSmarty->clearCache(null, $this->_aRequest['controller']);

			Log::insert($this->_aRequest['controller'],
									$this->_aRequest['action'],
									(int) $this->_aRequest['id'],
									$this->_aSession['userdata']['id']);

			return Helper::successMessage(I18n::get('success.destroy'), '/calendar');
		}
		else
			return Helper::errorMessage(I18n::get('error.sql'), '/calendar');
	}
}
<?php

/**
 * CRUD action of simple calendar.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 */

namespace CandyCMS\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Calendar as Model;

require_once 'app/models/Calendar.model.php';

class Calendar extends Main {

  /**
	 * Include the calendar model.
	 *
	 * @access public
	 * @override app/controllers/Main.controller.php
	 *
	 */
	public function __init() {
		$this->_oModel = new Model($this->_aRequest, $this->_aSession);
	}

	/**
	 * Show calendar overview.
	 *
	 * @access public
	 * @return string HTML content
	 *
	 */
	public function show() {
		$this->_aData = $this->_oModel->getData($this->_iId);
		$this->oSmarty->assign('calendar', $this->_aData);

		$sTemplateDir = Helper::getTemplateDir('calendars', 'show');
		$this->oSmarty->template_dir = $sTemplateDir;
		return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, 'show'));
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
			$this->_aData = $this->_oModel->getData($this->_iId, true);

		# Create
		else {
			$this->_aData['content'] = isset($this->_aRequest['content']) ? $this->_aRequest['content'] : '';
			$this->_aData['end_date'] = isset($this->_aRequest['end_date']) ? $this->_aRequest['end_date'] : '';
			$this->_aData['start_date'] = isset($this->_aRequest['start_date']) ? $this->_aRequest['start_date'] : '';
			$this->_aData['title'] = isset($this->_aRequest['title']) ? $this->_aRequest['title'] : '';
		}

		foreach ($this->_aData as $sColumn => $sData)
			$this->oSmarty->assign($sColumn, $sData);

		if (!empty($this->_aError))
			$this->oSmarty->assign('error', $this->_aError);

		$sTemplateDir = Helper::getTemplateDir('calendars', '_form');
		$this->oSmarty->template_dir = $sTemplateDir;
		return $this->oSmarty->fetch(Helper::getTemplateType($sTemplateDir, '_form'));
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
			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], $this->_oModel->getLastInsertId('calendars'), $this->_aSession['userdata']['id']);
			return Helper::successMessage($this->oI18n->get('success.create'), '/calendar');
		}
		else
			return Helper::errorMessage($this->oI18n->get('error.sql'), '/calendar');
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
			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id'], $this->_aSession['userdata']['id']);
			return Helper::successMessage($this->oI18n->get('success.update'), '/calendar');
		}
		else
			return Helper::errorMessage($this->oI18n->get('error.sql'), '/calendar');
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
			Log::insert($this->_aRequest['section'], $this->_aRequest['action'], (int) $this->_aRequest['id'], $this->_aSession['userdata']['id']);
			return Helper::successMessage($this->oI18n->get('success.destroy'), '/calendar');
		}
		else
			return Helper::errorMessage($this->oI18n->get('error.sql'), '/calendar');
	}
}
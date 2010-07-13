<?php

/*
 * This software is copyright protected. Use only allowed on licensed
 * websites. Contact author for further information or to receive a license.
 *
 * @link http://marcoraddatz.com
 * @copyright 2007 - 2008 Marco Raddatz
 * @author Marco Raddatz <mr at marcoraddatz dot com>
 * @package CMS
 * @version 1.0
 */

final class Addon extends Section {
	public final function __construct($aRequest, $oSession, $aFile = '') {
		$this->m_aRequest	=& $aRequest;
		$this->m_oSession	=& $oSession;
		$this->m_aFile		=& $aFile;

		$this->_setModules();
		$this->_getSection();
	}

	private final function _setModules() {
		$oDir = opendir('addon');

		while($aFile = readdir($oDir)) {
			if($aFile == '.' || $aFile == '..' || $aFile == 'Addon.class.php' || $aFile == '_dev')
				continue;

			require_once ('app/addon/'	.$aFile);
		}
	}

	private final function _getSection() {
		switch( strtolower( $this->m_aRequest['section']) ) {
		# Addons by Marco Raddatz
		/*	case 'menu':

				break;*/

			# Insert extern Addons here
			default:
			case '404':

				parent::_setContent(Helper::errorMessage(LANG_ERROR_GLOBAL_404));
				parent::_setTitle(LANG_ERROR_GLOBAL_404);

				break;
		}
	}
}
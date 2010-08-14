<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
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
			if($aFile == '.' || $aFile == '..' || $aFile == 'Addon.class.php' || $aFile == '_dev' || $aFile == '.htaccess')
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
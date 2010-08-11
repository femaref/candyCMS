<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

abstract class Model_Main {
	protected $m_aRequest;
	protected $_aData;
	protected $_iID;

	public function __construct($aRequest = '', $oSession = '', $aFile = '') {
		$this->m_aRequest	=& $aRequest;
		$this->m_oSession	=& $oSession;
		$this->m_aFile		=& $aFile;
	}

	public static function simpleQuery($sWhat, $sFrom, $sWhere, $iLimit = 0) {
		$oQuery = new Query("	SELECT
									"	.(string)$sWhat.	"
								FROM
									"	.(string)$sFrom.	"
								WHERE
									"	.(string)$sWhere.	"
								LIMIT
									"	.(int)$iLimit);

		if( $oQuery == true )
			return $oQuery->fetch();
		else
			return $oQuery->getError();
	}

	public static function simpleCount($sFrom, $sWhere = '', $sWhat = '*') {
		if( !empty($sWhere) )
			$sForm = $sFrom.	" WHERE "	.(string)$sWhere;

		$oQuery = new Query("SELECT COUNT(" .$sWhat.  ") FROM "	.(string)$sFrom);
		return $oQuery->count();
	}

	public function create() {
	}

	protected function update() {
	}

	public function destroy() {
	}
}
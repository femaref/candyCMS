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

	public static function simpleCount($sFrom, $sWhere = '') {
		if( !empty($sWhere) )
			$sForm = $sFrom.	" WHERE "	.(string)$sWhere;

		$oQuery = new Query("SELECT COUNT(*) FROM "	.(string)$sFrom);
		return $oQuery->count();
	}

	public function create() {
	}

	protected function update() {
	}

	public function destroy() {
	}
}
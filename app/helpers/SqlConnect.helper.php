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

final class SQLCONNECT {
	public final static function connect($sHost, $sUser, $sPassword) {
		$oConnect = @mysql_connect($sHost, $sUser, $sPassword);
		if(!$oConnect)
			die('<h1>'  .LANG_ERROR_GLOBAL. '</h1><h3>'  .
					LANG_ERROR_DB_CONNECTION.	'</h3>'	.mysql_error());
	}

	public final static function selectDB($sDatabase) {
		$oDb = @mysql_select_db($sDatabase);
		if(!$oDb)
			die('<h1>'  .LANG_ERROR_GLOBAL. '</h1><h3>'  .
					LANG_ERROR_DB_SELECTION.	'</h3>'	.mysql_error());
	}
}
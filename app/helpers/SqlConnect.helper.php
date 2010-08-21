<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
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
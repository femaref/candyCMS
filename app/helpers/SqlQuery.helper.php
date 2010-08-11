<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

final class Query
{
	private $_sSql = '';
	private $_oResult = 0;
	private $_sErrno = 0;
	private $_sError = '';

	public final function __construct($sSql)
	{
		$this->_sSql = trim($sSql);
		$this->_oResult = mysql_query($this->_sSql);

		if(!$this->_oResult)
		{
			$this->_sErrno = mysql_errno();
			$this->_sError = mysql_error();
			$this->getError();
		}

		if(WEBSITE_DEV == 1 && SQL_DEBUG == 1)
			return Helper::debugMessage($sSql);
	}

	private final function _error()
	{
 		$oError =& $this->_oResult;
		$bError = (bool)$oError;
		$bError = !$bError;
		return $bError;
	}

	public final function getError()
	{
		$sStr = '';
		if($this->_error())
		{
			$sStr  = "<h3>Query:</h3>\n"	.$this->_sSql;
			$sStr .= "<h3>Error:</h3>\n"	.$this->_sError;
			$sStr .= "<h3>Code:</h3>\n"	.$this->_sErrno;
		}

		return $sStr;
	}

	public final function fetch()
	{
		if($this->_error())
			$aSql = null;
		else
			$aSql = mysql_fetch_assoc($this->_oResult);

		return $aSql;
	}

	public final function fetchArray()
	{
		if($this->_error())
			$aSql = null;
		else
			$aSql = mysql_fetch_array($this->_oResult);

		return $aSql;
	}

	public final function numRows()
	{
		if($this->_error())
			$aSql = null;
		else
			$aSql = mysql_num_rows($this->_oResult);

		return $aSql;
	}

	public final function count()
	{
		if($this->_error())
			$aSql = null;
		else
			$aSql = mysql_result($this->_oResult, 0);

		return $aSql;
	}
}
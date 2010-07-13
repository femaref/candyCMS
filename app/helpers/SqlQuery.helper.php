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

final class Query
{
	private $_sSql = '';
	private $_oResult = 0;
	private $_sErrno = 0;
	private $_sError = '';

	public final function __construct($sSql)
	{
		if(WEBSITE_DEV == 1 && SQL_DEBUG == 1)
			echo Helper::debugMessage($sSql);

		$this->_sSql = trim($sSql);
		$this->_oResult = mysql_query($this->_sSql);

		if(!$this->_oResult)
		{
			$this->_sErrno = mysql_errno();
			$this->_sError = mysql_error();
			$this->getError();
		}
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
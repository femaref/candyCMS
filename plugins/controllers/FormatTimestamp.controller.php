<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

# This plugin rewrites the standard date into a nicer "today" / "yesterday"
# format.

namespace CandyCMS\Plugin;

use CandyCMS\Helper\I18n as I18n;

final class FormatTimestamp {

	public final function __construct() {
		$this->oI18n = new I18n('languages/' . WEBSITE_LANGUAGE . '/' . WEBSITE_LANGUAGE . '.language.yml');
	}

  private final function _setDate($iTime, $bDateOnly) {
    if(date('Ymd', $iTime) == date('Ymd', time())) {
      $sDay = $this->oI18n->get('global.today');
      $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);
    }
    elseif(date('Ymd', $iTime) == date('Ymd', (time()-60*60*24))) {
      $sDay = $this->oI18n->get('global.yesterday');
      $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);
    }
    else {
      $sDay = strftime(DEFAULT_DATE_FORMAT, $iTime);
      $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);
    }

    $sTime = str_replace('am', $this->oI18n->get('global.time.am'), $sTime);
    $sTime = str_replace('pm', $this->oI18n->get('global.time.pm'), $sTime);

    if ($bDateOnly == true)
      return $sDay;
    else
      return $sDay.$sTime;
  }

  public final function getDate($iTime, $bDateOnly) {
    return $this->_setDate($iTime, $bDateOnly);
  }
}
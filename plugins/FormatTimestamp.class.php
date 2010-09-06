<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

class FormatTimestamp {
  private final function _setDate($iTime) {
    if(date(DEFAULT_DATE_FORMAT, $iTime) == date(DEFAULT_DATE_FORMAT, time())) {
      $sDay = LANG_GLOBAL_TODAY;
      $sTime = date(DEFAULT_TIME_FORMAT, $iTime);
    }
    elseif(date(DEFAULT_DATE_FORMAT, $iTime) == date(DEFAULT_DATE_FORMAT, (time()-60*60*24))) {
      $sDay = LANG_GLOBAL_YESTERDAY;
      $sTime = date(DEFAULT_TIME_FORMAT, $iTime);
    }
    else {
      $sDay = date(DEFAULT_DATE_FORMAT, $iTime);
      $sTime = date(DEFAULT_TIME_FORMAT, $iTime);
    }

    $sTime = str_replace('am', LANG_GLOBAL_TIME_AM, $sTime);
    $sTime = str_replace('pm', LANG_GLOBAL_TIME_PM, $sTime);

    return $sDay.$sTime;
  }

  public final function getDate($iTime) {
    return $this->_setDate($iTime);
  }
}
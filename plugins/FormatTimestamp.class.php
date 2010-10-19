<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

class FormatTimestamp {
  private final function _setDate($iTime, $bDateOnly) {
    if(date('Ymd', $iTime) == date('Ymd', time())) {
      $sDay = LANG_GLOBAL_TODAY;
      $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);
    }
    elseif(date('Ymd', $iTime) == date('Ymd', (time()-60*60*24))) {
      $sDay = LANG_GLOBAL_YESTERDAY;
      $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);
    }
    else {
      $sDay = strftime(DEFAULT_DATE_FORMAT, $iTime);
      $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);
    }

    $sTime = str_replace('am', LANG_GLOBAL_TIME_AM, $sTime);
    $sTime = str_replace('pm', LANG_GLOBAL_TIME_PM, $sTime);

    if ($bDateOnly == true)
      return $sDay;
    else
      return $sDay.$sTime;
  }

  public final function getDate($iTime, $bDateOnly) {
    return $this->_setDate($iTime, $bDateOnly);
  }
}
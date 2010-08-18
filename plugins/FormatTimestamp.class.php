<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

class FormatTimestamp {
  private final function _setDate($iTime, $sStyle = ', ') {
    if(date('d.m.Y', $iTime) == date('d.m.Y', time())) {
      $sDay = LANG_GLOBAL_TODAY;
      $sStyle = ',&nbsp;';
      $sTime = date('H:i a', $iTime);
    }
    elseif(date('d.m.Y', $iTime) == date('d.m.Y', (time()-60*60*24))) {
      $sDay = LANG_GLOBAL_YESTERDAY;
      $sStyle = ',&nbsp;';
      $sTime = date('H:i a', $iTime);
    }
    else {
      $sDay = date('d.m.Y', $iTime);
      $sTime = date('H:i a', $iTime);
    }
    $sTime = str_replace('am', LANG_GLOBAL_TIME_AM, $sTime);
    $sTime = str_replace('pm', LANG_GLOBAL_TIME_PM, $sTime);

    return $sDay.$sStyle.$sTime;
  }

  public final function getDate($iTime) {
    return $this->_setDate($iTime);
  }
}
<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
*/

# This plugin rewrites the standard date into a nicer "today" / "yesterday"
# format.

namespace CandyCMS\Plugin;

use CandyCMS\Helper\I18n as I18n;

require_once 'app/helpers/I18n.helper.php';

final class FormatTimestamp {

  private final function _setDate($iTime, $bDateOnly) {
    if(date('Ymd', $iTime) == date('Ymd', time())) {
      $sDay = I18n::get('global.today');
      $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);
    }
    elseif(date('Ymd', $iTime) == date('Ymd', (time()-60*60*24))) {
      $sDay = I18n::get('global.yesterday');
      $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);
    }
    else {
      $sDay = strftime(DEFAULT_DATE_FORMAT, $iTime);
      $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);
    }

    $sTime = str_replace('am', I18n::get('global.time.am'), $sTime);
    $sTime = str_replace('pm', I18n::get('global.time.pm'), $sTime);

    return ($bDateOnly == true) ? $sDay : $sDay.$sTime;
  }

  public final function getDate($iTime, $bDateOnly) {
    return $this->_setDate($iTime, $bDateOnly);
  }
}
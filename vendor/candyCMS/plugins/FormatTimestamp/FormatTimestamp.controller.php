<?php

/**
 * This plugin rewrites the standard date into a nicer "today" / "yesterday" format.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.0
 *
 *
 */

namespace CandyCMS\Plugins;

use CandyCMS\Core\Helpers\I18n;

final class FormatTimestamp {

  /**
   *
   * @param type $iTime
   * @param type $iOptions
   * @return type
   */
  private final function _setDate($iTime, $iOptions) {
    if(!$iTime)
      return;

    $sTime = strftime(DEFAULT_TIME_FORMAT, $iTime);

    if(date('Ymd', $iTime) == date('Ymd', time()))
      $sDay = I18n::get('global.today');

    elseif(date('Ymd', $iTime) == date('Ymd', (time()-60*60*24)))
      $sDay = I18n::get('global.yesterday');

    else
      $sDay = strftime(DEFAULT_DATE_FORMAT, $iTime);

    $sTime = str_replace('am', I18n::get('global.time.am'), $sTime);
    $sTime = str_replace('AM', I18n::get('global.time.am'), $sTime);
    $sTime = str_replace('pm', I18n::get('global.time.pm'), $sTime);
    $sTime = str_replace('PM', I18n::get('global.time.pm'), $sTime);

    if($iOptions == 1)
      return $sDay;

    elseif($iOptions == 2)
      return $sTime;

    else
      return $sDay . ', ' . $sTime;
  }

  public final function getDate($iTime, $bDateOnly) {
    return $this->_setDate($iTime, $bDateOnly);
  }
}
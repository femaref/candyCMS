<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# The archive plugin lists all blog entries by month and date.

namespace CandyCMS\Plugin;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use CandyCMS\Model\Blog as Model;
use Smarty;

require_once 'app/models/Blog.model.php';

final class Archive {

  public final function show() {
    $oModel = new Model();
    $aData = $oModel->getData('', false, PLUGIN_ARCHIVE_LIMIT);

    foreach ($aData as $aRow) {
			# Date format the month
			$sMonth = strftime('%m', $aRow['date_raw']);
			$sMonth = substr($sMonth, 0, 1) == 0 ? substr($sMonth, 1, 2) : $sMonth;
      $sMonth = I18n::get('global.months.' . $sMonth) . ' ' . strftime('%Y', $aRow['date_raw']);

			# Prepare array
			$iId = $aRow['id'];
      $aMonth[$sMonth][$iId] = $aRow;
    }

    $oSmarty = new Smarty();
    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;

    $oSmarty->assign('data', $aMonth);

    $oSmarty->template_dir = Helper::getPluginTemplateDir('archive', 'show');
    return $oSmarty->fetch('show.tpl');
  }
}
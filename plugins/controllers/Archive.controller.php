<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# The archive plugin lists all blog entries by month and date.

namespace CandyCMS\Plugin;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use Smarty;

require_once 'app/controllers/Blog.controller.php';

final class Archive extends \CandyCMS\Controller\Blog {

  public final function show() {
    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $this->oSmarty->setCacheLifetime(300);
    $this->oSmarty->setCompileCheck(false);
    $this->oSmarty->template_dir = Helper::getPluginTemplateDir('archive', 'show');

    if (!$this->oSmarty->isCached('show.tpl')) {
      $aData = $this->_oModel->getData('', false, PLUGIN_ARCHIVE_LIMIT);

      $aMonth = array();
      foreach ($aData as $aRow) {
        # Date format the month
        $sMonth = strftime('%m', $aRow['date_raw']);
        $sMonth = substr($sMonth, 0, 1) == 0 ? substr($sMonth, 1, 2) : $sMonth;
        $sMonth = I18n::get('global.months.' . $sMonth) . ' ' . strftime('%Y', $aRow['date_raw']);

        # Prepare array
        $iId = $aRow['id'];
        $aMonth[$sMonth][$iId] = $aRow;
      }

      $this->oSmarty->assign('data', $aMonth);
    }

    return $this->oSmarty->fetch('show.tpl');
  }
}
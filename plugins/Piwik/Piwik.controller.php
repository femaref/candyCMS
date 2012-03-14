<?php

/**
 * Piwik analytics code.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Hauke Schade <http://hauke-schade.de>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Plugin\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\SmartySingleton as SmartySingleton;

final class Piwik {

  /**
   * Identifier for Template Replacements
   *
   * @var constant
   *
   */
  const IDENTIFIER = 'piwik';

  public final static function show() {
    $sTemplateDir   = Helper::getPluginTemplateDir('piwik', 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

    $oSmarty = SmartySingleton::getInstance();
    $oSmarty->setTemplateDir($sTemplateDir);

    if (!$oSmarty->isCached($sTemplateFile, 'layouts|' . WEBSITE_LOCALE . '|piwik')) {
      $oSmarty->assign('WEBSITE_MODE', WEBSITE_MODE);
      $oSmarty->assign('PLUGIN_PIWIK_ID', PLUGIN_PIWIK_ID);
      $oSmarty->assign('PLUGIN_PIWIK_URL', PLUGIN_PIWIK_URL);
    }

    return $oSmarty->fetch($sTemplateFile, 'layouts|' . WEBSITE_LOCALE . '|piwik');
  }
}
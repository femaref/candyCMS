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
use Smarty;

final class Piwik {

  public final static function show() {
    $sTemplateDir   = Helper::getPluginTemplateDir('piwik', 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

    $oSmarty = new Smarty();
    $oSmarty->setCacheDir(PATH_STANDARD . '/' . CACHE_DIR);
    $oSmarty->setCompileDir(PATH_STANDARD . '/' . COMPILE_DIR);
    $oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $oSmarty->setTemplateDir($sTemplateDir);

    $oSmarty->merge_compiled_includes = true;
    $oSmarty->use_sub_dirs = true;

    if (!$oSmarty->isCached($sTemplateFile, 'layouts|' . WEBSITE_LOCALE . '|piwik')) {
      $oSmarty->assign('WEBSITE_MODE', WEBSITE_MODE);
      $oSmarty->assign('PLUGIN_PIWIK_ID', PLUGIN_PIWIK_ID);
      $oSmarty->assign('PLUGIN_PIWIK_URL', PLUGIN_PIWIK_URL);
    }

    return $oSmarty->fetch($sTemplateFile, 'layouts|' . WEBSITE_LOCALE . '|piwik');
  }
}
<?php

/**
 * Cache analytics code.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Plugin\Controller;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\SmartySingleton as SmartySingleton;

final class Analytics {

  /**
   * Identifier for Template Replacements
   */
  const IDENTIFIER = 'analytics';

  public final static function show() {
    $sTemplateDir   = Helper::getPluginTemplateDir('analytics', 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

		$oSmarty = SmartySingleton::getInstance();
		$oSmarty->setTemplateDir($sTemplateDir);

		if (!$oSmarty->isCached($sTemplateFile, 'layouts|' . WEBSITE_LOCALE . '|analytics')) {
			$oSmarty->assign('WEBSITE_MODE', WEBSITE_MODE);
			$oSmarty->assign('PLUGIN_ANALYTICS_TRACKING_CODE', PLUGIN_ANALYTICS_TRACKING_CODE);
		}

		return $oSmarty->fetch($sTemplateFile, 'layouts|' . WEBSITE_LOCALE . '|analytics');
	}
}
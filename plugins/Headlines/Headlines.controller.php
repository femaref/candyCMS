<?php

/**
 * Show blog headlines.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.5
 *
 */

namespace CandyCMS\Plugin\Controller;

use CandyCMS\Helper\Helper as Helper;
use Smarty;

final class Headlines {

  /**
   * Identifier for Template Replacements
   */
  const identifier = 'headlines';

	/**
	 * Show the (cached) headlines.
	 *
	 * We use a new Smarty instance to avoid parsing the Main.controller due to performance reasons.
	 *
	 * @static
	 * @access public
	 * @param array $aRequest
	 * @param array $aSession
	 * @return string HTML
	 *
	 */
  public final static function show($aRequest, $aSession) {
    $sTemplateDir   = Helper::getPluginTemplateDir('headlines', 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

		$oSmarty = new Smarty();
		$oSmarty->setCacheDir(PATH_STANDARD . '/' . CACHE_DIR);
		$oSmarty->setCompileDir(PATH_STANDARD . '/' . COMPILE_DIR);
    $oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $oSmarty->setTemplateDir($sTemplateDir);

		$oSmarty->merge_compiled_includes = true;
		$oSmarty->use_sub_dirs = true;

    if (!$oSmarty->isCached($sTemplateFile, 'blog|' . WEBSITE_LOCALE . '|headlines')) {
			require_once PATH_STANDARD . '/app/models/Blog.model.php';
			$oModel = new \CandyCMS\Model\Blog($aRequest, $aSession);

			$oSmarty->assign('data', $oModel->getData('', false, PLUGIN_HEADLINES_LIMIT));
		}

    return $oSmarty->fetch($sTemplateFile, 'blog|' . WEBSITE_LOCALE . '|headlines');
  }
}
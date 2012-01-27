<?php

/**
 * Simply show an adsense template.
 *
 * This plugin loads a template with your adsense code. Copy your code from Google and
 * paste it into "public/skins/_plugins/adsense.tpl".
 *
 * You can include your plugin via "<!-- plugin:adsense -->".
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

namespace CandyCMS\Plugin;

use CandyCMS\Helper\Helper as Helper;

final class Adsense {

  /**
   * Show template.
   *
   * We set up a new Smarty, because it's faster than to extend the class.
   *
   * @access public
   * @return string HTML
   *
   */
  public final function show() {
    $oSmarty = new \Smarty();
    $oSmarty->cache_dir   = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;

    $oSmarty->template_dir = Helper::getPluginTemplateDir('adsense', 'show');
    return $oSmarty->fetch('show.tpl');
  }
}
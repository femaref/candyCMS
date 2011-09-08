<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# This plugin loads a template with your adsense code. Copy your code from Google and
# paste it into "public/skins/_plugins/adsense.tpl".
# You can include your plugin via "{$_plugin_adsense_}".
# This does only work at the main template ("app/views/layouts/application.tpl").

namespace CandyCMS\Plugin;

class Adsense {

  public function show() {
    $oSmarty = new \Smarty();
    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;

    $oSmarty->template_dir = \CandyCMS\Helper\Helper::getPluginTemplateDir('adsense', 'show');
    return $oSmarty->fetch('show.tpl');
  }
}
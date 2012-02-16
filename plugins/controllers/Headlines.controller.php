<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

namespace CandyCMS\Plugin;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use Smarty;

require_once PATH_STANDARD . '/app/controllers/Blog.controller.php';

# Show the last six headlines of your blog entries.
final class Headlines extends \CandyCMS\Controller\Blog {

  public final function show() {
    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $this->oSmarty->setCacheLifetime(300);
    $this->oSmarty->setCompileCheck(false);
    $this->oSmarty->template_dir = Helper::getPluginTemplateDir('headlines', 'show');

    if (!$this->oSmarty->isCached('show.tpl'))
      $this->oSmarty->assign('data', $this->_oModel->getData('', false, PLUGIN_HEADLINES_LIMIT));

    return $this->oSmarty->fetch('show.tpl');
  }
}
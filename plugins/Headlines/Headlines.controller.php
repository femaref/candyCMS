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

  public final function __init() {
    $oModel = $this->__autoload('Blog', true);
    $this->_oModel = new $oModel($this->_aRequest, $this->_aSession);
  }

  /**
   * @todo
   * @return type
   */
  public final function show() {
    $sTemplateDir   = Helper::getPluginTemplateDir('headlines', 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $this->oSmarty->setTemplateDir($sTemplateDir);

    if (!$this->oSmarty->isCached($sTemplateFile, 'blog|headlines|' . $this->_aSession['userdata']['role']))
      $this->oSmarty->assign('data', $this->_oModel->getData('', false, PLUGIN_HEADLINES_LIMIT));

    return $this->oSmarty->fetch($sTemplateFile, 'blog|headlines|' . $this->_aSession['userdata']['role']);
  }
}
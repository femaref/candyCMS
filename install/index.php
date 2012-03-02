<?php

/**
 * Website entry.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @version 2.0
 * @since 1.0
 *
 */

namespace CandyCMS;

use CandyCMS\Controller\Index as Index;
use PDO;
use Smarty;

define('PATH_STANDARD', dirname(__FILE__) . '/..');

require PATH_STANDARD . '/config/Candy.inc.php';
require PATH_STANDARD . '/app/controllers/Index.controller.php';

class Install extends Index {

  public function __init() {
		require PATH_STANDARD . '/app/models/Main.model.php';
		require PATH_STANDARD . '/app/controllers/Main.controller.php';
		require PATH_STANDARD . '/app/helpers/AdvancedException.helper.php';
		require PATH_STANDARD . '/app/helpers/I18n.helper.php';
		require PATH_STANDARD . '/lib/smarty/Smarty.class.php';
		require PATH_STANDARD . '/plugins/controllers/Cronjob.controller.php';

    # Load cronjob and start it.
    # TODO: enable
    #$this->getCronjob(true);

    $this->oSmarty = new Smarty();
		$this->oSmarty->cache_dir			= PATH_STANDARD . '/' . CACHE_DIR;
		$this->oSmarty->compile_dir		= PATH_STANDARD . '/' . COMPILE_DIR;
    $this->oSmarty->template_dir	= PATH_STANDARD . '/install/views';

    # Direct actions
    if (isset($this->_aRequest['action']) && 'install' == $this->_aRequest['action'])
      $this->showInstall();

    elseif (isset($this->_aRequest['action']) && 'migrate' == $this->_aRequest['action'])
      $this->showStart();

    else
      $this->showIndex();
  }

  public function showInstall() {
    switch ($this->_aRequest['step']) {

      default:
      case '1':
        $this->oSmarty->assign('title', 'Installation - Step 1');
        $this->oSmarty->assign('content', $this->oSmarty->fetch('install/step1.tpl'));
        break;

      case '2':
        break;

      case '3':
        break;

      case '4':
        break;
    }
  }

  public function showIndex() {
    $this->oSmarty->assign('title', 'Welcome!');
    $this->oSmarty->assign('content', $this->oSmarty->fetch('index.tpl'));
  }

  public function show() {
    return $this->oSmarty->fetch('layout.tpl');
  }
}

$oInstall = new Install(array_merge($_GET, $_POST));
$oInstall->__init();

echo $oInstall->show();

?>
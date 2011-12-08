<?php

/**
 * System tests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @version 2.0
 * @since 2.0
 */

require_once('lib/simpletest/autorun.php');

require_once 'app/models/Main.model.php';
require_once 'app/controllers/Main.controller.php';
require_once 'app/controllers/Session.controller.php';
require_once 'app/controllers/Index.controller.php';
require_once 'app/controllers/Log.controller.php';
require_once 'app/helpers/AdvancedException.helper.php';
require_once 'app/helpers/Section.helper.php';
require_once 'app/helpers/Helper.helper.php';
require_once 'app/helpers/I18n.helper.php';
require_once 'lib/smarty/Smarty.class.php';

require_once 'config/Candy.inc.php';

define('VERSION', '0');
define('AJAX_REQUEST', false);
define('CLEAR_CACHE', true);
define('CURRENT_URL', 'http://localhost/');
define('MOBILE', false);
define('MOBILE_DEVICE', false);

class AllTests extends TestSuite {

  function AllTests() {
    $this->TestSuite('All tests');
    $this->addFile('app/tests/Index.controller.php');
    $this->addFile('app/tests/Blog.model.php');
    $this->addFile('app/tests/Calendar.model.php');
    $this->addFile('app/tests/Comment.model.php');
    $this->addFile('app/tests/Content.model.php');
    $this->addFile('app/tests/Download.model.php');
    $this->addFile('app/tests/Gallery.model.php');
    $this->addFile('app/tests/Log.model.php');
    $this->addFile('app/tests/Search.model.php');
    $this->addFile('app/tests/Session.model.php');
    #$this->addFile('app/tests/Rss.controller.php');
  }
}
?>
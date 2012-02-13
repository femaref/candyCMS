<?php

/**
 * System tests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @version 2.0
 * @since 2.0
 */
require_once 'lib/simpletest/autorun.php';

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

if(WEBSITE_MODE == 'production')
  die('No tests in production mode.');

define('VERSION', '0');
define('AJAX_REQUEST', false);
define('CLEAR_CACHE', true);
define('CURRENT_URL', 'http://localhost/');
define('MOBILE', false);
define('MOBILE_DEVICE', false);

class AllTests extends TestSuite {

  function AllTests() {
    $this->TestSuite('All tests');

    # Init tests
    $this->addFile('tests/controllers/Index.controller.php');

    # SQL unit tests
    $this->addFile('tests/models/Blog.model.php');
    $this->addFile('tests/models/Calendar.model.php');
    $this->addFile('tests/models/Comment.model.php');
    $this->addFile('tests/models/Content.model.php');
    $this->addFile('tests/models/Download.model.php');
    $this->addFile('tests/models/Gallery.model.php');
    $this->addFile('tests/models/Log.model.php');
    $this->addFile('tests/models/Search.model.php');
    $this->addFile('tests/models/Session.model.php');
    $this->addFile('tests/models/User.model.php');

    # Web tests
    $this->addFile('tests/controllers/Download.controller.php');
    $this->addFile('tests/controllers/Gallery.controller.php');
    $this->addFile('tests/controllers/Media.controller.php');
    $this->addFile('tests/controllers/Session.controller.php');
    $this->addFile('tests/controllers/User.controller.php');
  }
}
?>
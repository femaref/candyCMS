<?php

/**
 * System tests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @version 2.0
 * @since 2.0
 *
 */
define('PATH_STANDARD', dirname(__FILE__) . '/..');

require_once PATH_STANDARD . '/tests/simpletest/autorun.php';
require_once PATH_STANDARD . '/tests/simpletest/web_tester.php';

require_once PATH_STANDARD . '/tests/candy/Candy.unit.php';
require_once PATH_STANDARD . '/tests/candy/Candy.web.php';

require_once PATH_STANDARD . '/config/Candy.inc.php';

require_once PATH_STANDARD . '/app/helpers/I18n.helper.php';
require_once PATH_STANDARD . '/lib/smarty/Smarty.class.php';

define('AJAX_REQUEST', false);
define('CLEAR_CACHE', true);
define('CURRENT_URL', 'http://localhost/');
define('MOBILE', false);
define('MOBILE_DEVICE', false);
define('UNIQUE_ID', 'tests');
define('VERSION', '0');

class AllFileTests extends TestSuite {

	function __construct() {
		parent::__construct();
		$this->TestSuite('Sections');

		# Index
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/unit/Index.controller.php');
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/web/Index.controller.php');

		# Download
		$this->addFile(PATH_STANDARD . '/tests/tests/app/models/unit/Download.model.php');
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/web/Download.controller.php');

		# Error
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/web/Error.controller.php');

		# Gallery
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/web/Gallery.controller.php');

		# Search
		$this->addFile(PATH_STANDARD . '/tests/tests/app/models/unit/Search.model.php');
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/web/Search.controller.php');
	}
}

?>
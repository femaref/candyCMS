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
		$this->TestSuite('All tests');

    # Helpers
    $this->addFile(PATH_STANDARD . '/tests/tests/app/helpers/Helper.helper.php');
    $this->addFile(PATH_STANDARD . '/tests/tests/app/helpers/I18n.helper.php');
    $this->addFile(PATH_STANDARD . '/tests/tests/app/helpers/Image.helper.php');

		# Index
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Index.controller.php');


    # Blog
    #$this->addFile(PATH_STANDARD . '/tests/tests/app/models/Blog.model.php');
    $this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Blog.controller.php');

    # Comment

    # Content

		# Download
		$this->addFile(PATH_STANDARD . '/tests/tests/app/models/Download.model.php');
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Download.controller.php');

		# Error
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Error.controller.php');

		# Gallery
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Gallery.controller.php');

    # Log

    # Mail

    # Media

    # Newsletter

    # RSS

		# Search
		$this->addFile(PATH_STANDARD . '/tests/tests/app/models/Search.model.php');
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Search.controller.php');

    # Session

    # Sitemap

    # User*/
	}
}

?>
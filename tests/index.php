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
    #$this->addFile(PATH_STANDARD . '/tests/tests/app/helpers/Helper.helper.php');
    #$this->addFile(PATH_STANDARD . '/tests/tests/app/helpers/I18n.helper.php');
    #$this->addFile(PATH_STANDARD . '/tests/tests/app/helpers/Image.helper.php');

		# Index
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Index.controller.php');


    # Blog
    #$this->addFile(PATH_STANDARD . '/tests/tests/app/models/Blog.model.php');
    #$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Blog.controller.php');

    # Comment
    $this->addFile(PATH_STANDARD . '/tests/tests/app/models/Comment.model.php');
    $this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Comment.controller.php');

    # Content
    #$this->addFile(PATH_STANDARD . '/tests/tests/app/models/Content.model.php');
    #$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Content.controller.php');

		# Download
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/models/Download.model.php');
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Download.controller.php');

		# Error
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Error.controller.php');

		# Gallery
    #$this->addFile(PATH_STANDARD . '/tests/tests/app/models/Gallery.model.php');
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Gallery.controller.php');

    # Log
    $this->addFile(PATH_STANDARD . '/tests/tests/app/models/Log.model.php');
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Log.controller.php');

    # Mail
		$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Mail.controller.php');


    # Media
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Media.controller.php');

    # Newsletter
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Newsletter.controller.php');

    # RSS
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Rss.controller.php');

		# Search
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/models/Search.model.php');
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Search.controller.php');

    # Session

    # Sitemap
		#$this->addFile(PATH_STANDARD . '/tests/tests/app/controllers/Sitemap.controller.php');

    # User
	}
}

?>
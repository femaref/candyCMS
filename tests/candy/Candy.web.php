<?php

/**
 * PHP unit tests.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */

require_once PATH_STANDARD . '/app/controllers/Main.controller.php';
require_once PATH_STANDARD . '/app/models/Main.model.php';

require_once PATH_STANDARD . '/app/helpers/I18n.helper.php';

use \CandyCMS\Helper\I18n as I18n;

abstract class CandyWebTest extends WebTestCase {

	public $oObject;

	public $aRequest;
	public $aSession;
	public $aFile;
	public $aCookie;

  public $iLastInsertId;

	function __construct() {
		parent::__construct();

		$this->aRequest	= array('section' => 'blog', 'clearcache' => 'true');
		$this->aFile			= array();
		$this->aCookie		= array();
		$this->aSession['user'] = array(
				'email' => '',
				'facebook_id' => '',
				'id' => 0,
				'name' => '',
				'surname' => '',
				'password' => '',
				'role' => 0,
				'full_name' => ''
		);
	}

	function createFile($sPath) {
		$sFile = PATH_STANDARD . '/' . $sPath . '/test_generated.log';
		$oFile = fopen($sFile, 'a');
		fwrite($oFile, 'Is writeable.' . "\n");
		fclose($oFile);

		return $sFile;
	}

	function removeFile($sPath) {
		return unlink(PATH_STANDARD . '/' . $sPath . '/test_generated.log');
	}

  function assert404() {
    $this->assertText(\CandyCMS\Helper\I18n::get('error.404.title'));
    # $this->assertResponse(404);
  }

  function loginAsUserWithRole($role = 4) {
    switch ($role) {
      case 1:
        $email = 'member@example.com';
        break;
      case 2:
        $email = 'facebook@example.com';
        break;
      case 3:
        $email = 'moderator@example.com';
        break;
      default:
      case 4:
        $email = 'admin@example.com';
        break;
    }
    # we need redirects for this
    $this->setMaximumRedirects(3);
    $this->post(WEBSITE_URL . '/sessions/create',
            array('email' => $email,
                  'password' => 'test',
                  'create_sessions' => 'formadata'));
    $this->assertText(I18n::get('success.session.create'));
  }

  function logout() {
    # we need redirects for this
    $this->setMaximumRedirects(3);
    $this->assertTrue($this->get(WEBSITE_URL . '/sessions/destroy'));
    $this->assertText(I18n::get('success.session.destroy'));
  }

}
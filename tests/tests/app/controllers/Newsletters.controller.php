<?php

/**
 * PHP unit tests
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 2.0
 *
 */
require_once PATH_STANDARD . '/app/controllers/Main.controller.php';
require_once PATH_STANDARD . '/app/controllers/Newsletters.controller.php';

use \CandyCMS\Controller\Newsletters as Newsletters;
use \CandyCMS\Helper\I18n as I18n;

class WebTestOfNewsletterController extends CandyWebTest {

  function setUp() {
    $this->aRequest['controller'] = 'newsletters';
    $this->oObject = new Newsletters($this->aRequest, $this->aSession);
  }

  function tearDown() {
    parent::tearDown();
  }

  function testCreate() {
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/create'));
    $this->assertResponse(200);
    $this->assertText(I18n::get('newsletters.title.subscribe'));
  }

  function testShow() {
    #should redirect to create
    $this->setMaximumRedirects(0);
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller']));
    $this->assertResponse(302);
  }

  /**
   * @todo validate forms
   */
  function testSubscribe() {
    # Correct email
    $this->post(WEBSITE_URL . '/' . $this->aRequest['controller'], array(
        'name' => md5($this->aSession['user']['name'] . time()),
        'surname' => md5($this->aSession['user']['surname'] . time()),
        'email' => time() . '_' . WEBSITE_MAIL_NOREPLY,
        'subscribe_newsletter' => 'formdata'
    ));

    $this->assertText(I18n::get('success.newsletter.create'));
    $this->assertResponse(200);

    # Wrong email address
    $this->post(WEBSITE_URL . '/' . $this->aRequest['controller'], array(
        'name' => md5($this->aSession['user']['name'] . time()),
        'surname' => md5($this->aSession['user']['surname'] . time()),
        'email' => str_replace('@', '', WEBSITE_MAIL_NOREPLY),
        'subscribe_newsletter' => 'formdata'
    ));

    $this->assertText(I18n::get('error.standard'));
    $this->assertResponse(200);
  }

  function testUpdate() {
    # there is no update
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/update'));
    $this->assert404();
  }

  function testDestroy() {
    # there is no destroy
    $this->assertTrue($this->get(WEBSITE_URL . '/' . $this->aRequest['controller'] . '/1/destroy'));
    $this->assert404();
  }
}
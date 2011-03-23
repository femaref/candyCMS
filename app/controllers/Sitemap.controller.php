<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Blog.model.php';
require_once 'app/models/Content.model.php';

class Sitemap extends Main {

  public function __init() {
    Header('Content-Type: text/xml');
  }

  public final function show() {
    # start
    $sWebsiteLandingPage = WEBSITE_URL . '/' . WEBSITE_LANDING_PAGE;


    # blog
    $oBlog = new Model_Blog();
    $aBlog = $oBlog->getData('', false, 1000);


    # content
    $oContent = new Model_Content();
    $aContent = $oContent->getData('', false);

    # gallery

    # projects

    $this->_oSmarty->assign('_website_landing_page_', $sWebsiteLandingPage);

    $this->_oSmarty->assign('blog', $aBlog);
    $this->_oSmarty->assign('content', $aContent);



		$this->_oSmarty->template_dir = Helper::getTemplateDir('sitemaps/xml');
		return $this->_oSmarty->fetch('sitemaps/xml.tpl');
  }
}
<?php

/**
 * Addon dispatcher.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.5
 */

namespace CandyCMS\Addon\Controller;

use CandyCMS\Helper\Helper as Helper;

if (!class_exists('\CandyCMS\Helper\Section'))
  require_once 'app/helpers/Section.helper.php';

class Addon extends \CandyCMS\Helper\Section {

  public function getSection() {

    switch (strtolower($this->_aRequest['section'])) {
      default:
      case '404':

        # There is no such requested addon
        header('Status: 404 Not Found');
				header("HTTP/1.0 404 Not Found");
        Helper::redirectTo('/error/404');

        break;

      # This is a sample addon to manage a downloads section
      case 'sample':

        require_once 'addons/controllers/Sample.controller.php';
        $oSample = new Addon_Sample($this->_aRequest, $this->_aSession, $this->_aFile);

        parent::_setContent($oSample->show());
        parent::_setDescription('My description');
        parent::_setKeywords('Keyword,Keyword,Keyword');
        parent::_setTitle('Sample');

        break;

      # Enter your addons here
    }
  }
}
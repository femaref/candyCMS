<?php

/**
 * Addon dispatcher.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @license MIT
 * @since 1.5
 * @see app/helpers/Dispatcher.helper.php
 */

namespace CandyCMS\Addon\Controller;

use CandyCMS\Helper\Helper as Helper;

if (!class_exists('\CandyCMS\Helper\Dispatcher'))
  require_once 'app/helpers/Dispatcher.helper.php';

class Addon extends \CandyCMS\Helper\Dispatcher {

  public function getAction() {

    switch (strtolower($this->_aRequest['section'])) {
      default:
      case '404':

        # There is no such requested addon
        header('Status: 404 Not Found');
				header("HTTP/1.0 404 Not Found");
        Helper::redirectTo('/error/404');

        break;

      # This is an example of how to create a addon.
      # For more information take a look at "app/helpers/Dispatcher.helper.php"
      case 'sample':

        require_once 'addons/controllers/Sample.controller.php';
        $this->_oObject = new Addon_Sample($this->_aRequest, $this->_aSession, $this->_aFile);
        $this->_oObject->__init();

        parent::_setContent($this->_oObject->show());
        parent::_setDescription('My description');
        parent::_setKeywords('Keyword,Keyword,Keyword');
        parent::_setTitle('Sample');

        break;

      # Enter your addons here
    }
  }
}
<?php

/*
 * This software is licensed under GPL <http://www.gnu.org/licenses/gpl.html>.
 *
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

/* This is an example for extending a standard class. */
class Addon_Mail extends Mail {

  public function create() {
    die('Your custom function here.');
  }
}
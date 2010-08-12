<?php

/**
 * The SmtpConnect class enables sending mails via SMTP.
 * It is not very easily operated, but that is not required,
 * because this class should just be the base for an comfortable
 * Mail class. On the basis of some comments to this class i want
 * to add that it sure supports Secure Sockets Layer connections.
 * You just have to modify the host and port in the constructor.
 *
 * @author Andreas Wilhelm <Andreas2209@web.de>
 * @copyright Andreas Wilhelm
 * @version 13/02/2009
 * @see http://avedo.net
 */
class SmtpConnect {
  /**
   * @var String $host The host you want to connect to
   * @access private
   */
  private $host;

  /**
   * @var Integer $port The http server port
   * @access private
   */
  private $port;

  /**
   * @var Object $sock Holds the socket connection
   * @access private
   */
  private $sock;

  /**
   * @var Array $authTypes Types of authetification
   * @access private
   */
  private $authTypes = array(
          'LOGIN',
          'PLAIN',
          'CRAM-MD5');

  /**
   * @var String $response The last server response
   * @access private
   */
  private $response = '';

  /**
   * @var String $log Holds the connection logging
   * @access private
   */
  private $log = '';

  /**
   * Sets host and port
   *
   * @access public
   * @param String $host Host name
   * @param Integer $port The server Port
   * @return void
   */
  public function __construct($host='localhost', $port=25) {
    // set server-variables
    $this->host = $host;
    $this->port = $port;
  }

  /**
   * Connects to the given server
   *
   * @access public
   * @return Boolean
   */
  public function connect() {
    // control-connection handle is saved to $handle
    $this->sock = @fsockopen($this->host, $this->port);
    if ( !$this->sock OR !$this->check('220') )
      throw new Exception("Connection failed.");

    // switch to non-blocking mode - just return data no re
    stream_set_blocking($this->sock, true); // by Marco Raddatz

    // set timeout of the server connection
    stream_set_timeout($this->sock, 0, 200000);

    return true;
  }

  /**
   * Sends greeting to secured server
   *
   * @access public
   * @return Boolean
   */
  public function ehlo() {
    // send EHLO -spezified in RFC 2554
    $this->cmd("EHLO " . $this->host);
    if( !$this->check('250') )
      throw new Exception("Failed to send EHLO.");

    return true;
  }

  /**
   * Sends greeting to server
   *
   * @access public
   * @return Boolean
   */
  public function helo() {
    // Send the RFC821 specified HELO.
    $this->cmd('HELO ' . $this->host);
    if( !$this->check('250') )
      throw new Exception("Failed to send HELO.");

    return true;
  }

  /**
   * Sends authentification
   *
   * @access public
   * @param String $user The username
   * @param String $pwd The passwort
   * @param String $type The authetification Type (LOGIN/PLAIN/CRAM-MD5)
   * @return Boolean
   */
  public function auth($user, $pwd, $type='PLAIN') {
    if( in_array($type, $this->authTypes) ) {
      // send authentification-identifier
      $this->cmd("AUTH $type");

      // catch first ready response
      $response = $this->getReply();
      if( substr($response,0,1) != 3 ) {
        throw new Exception("Failed to send AUTH.");
      }
    }

    if( $type == 'LOGIN' ) {
      // send user-name
      $this->cmd(base64_encode($user));
      if( !$this->check('334') )
        throw new Exception("Failed to send user-name.");

      // send password
      $this->cmd(base64_encode($pwd));
    }

    elseif( $type == 'PLAIN' ) {
      // prepare data
      $data = base64_encode($user.chr(0).$user.chr(0).$pwd);
      $this->cmd($data);
    }

    elseif( $type == 'CRAM-MD5' ) {
      $data = explode(' ',$response);
      $data = base64_decode($data[1]);
      $key = str_pad($pwd, 64, chr(0x00));
      $ipad = str_repeat(chr(0x36), 64);
      $opad = str_repeat(chr(0x5c), 64);
      $this->cmd( base64_encode($user.' '.md5(($key ^ $opad).md5(($key ^ $ipad).$data,true))) );
    }

    else
      throw new Exception("Authentification failed.");

    if( !$this->check('235') ) {
      throw new Exception("Authentification failed.");
    }

    return true;
  }

  /**
   * Sends specified addressor
   *
   * @access public
   * @param String $from The email-address of the addressor
   * @return Boolean
   */
  public function from($from) {
    // specify addressor
    $this->cmd("MAIL FROM: $from");
    if( !$this->check('250') )
      throw new Exception("Failed to send addressor.");

    return true;
  }

  /**
   * Sends specified acceptor
   *
   * @access public
   * @param String $to The email-address of the acceptor
   * @return Boolean
   */
  public function rcpt($to) {
    // send specified acceptor
    $this->cmd("RCPT TO: $to");
    if( !$this->check('250') )
      throw new Exception("Failed to send acceptor.");

    return true;
  }

  /**
   * Sends the data to the server
   *
   * @access public
   * @param String $message The message
   * @param Array $header SOme more mail header fields
   * @return void
   */
  public function data($message, $header) {
    // initiate data-transfere
    $this->cmd('DATA');
    if( !$this->check('354') )
      throw new Exception("Data-transfere failed.");

    // validate header-data
    if( !is_array($header) )
      throw new Exception("Header-data must be an array.");

    // initiate counter
    $i = 0;

    // include header data
    foreach( $header as $key => $value) {
      // send header
      if( $i < count($header)-1 ) {
        $this->cmd("$key: $value");
      }

      else {
        $this->cmd("$key: $value\r\n");
      }

      $i++;
    }

    // send the message
    $this->cmd("$message\r\n");

    // send end parameter
    $this->cmd('.');

    $this->check('250');
  }

  /**
   * Closes the server-connection
   *
   * @access public
   * @return void
   */
  public function quit() {
    $this->cmd("QUIT");
    $this->check('221');
    fclose($this->sock);
    return true;
  }

  /**
   * Sets a ftp-command given by the user
   *
   * @access private
   * @param String $cmd A specific command
   * @return void
   */
  private function cmd($cmd) {
    fputs($this->sock, "$cmd\r\n");
    $this->log("> $cmd");
  }

  /**
   * Catches the reply of the server
   *
   * @access private
   * @return String
   */
  private function getReply() {
    $go = true;
    $message = "";

    do {
      $tmp = @fgets($this->sock, 1024);
      if($tmp === false) {
        $go = false;
      }

      else {
        $message .= $tmp;
        if( preg_match('/^([0-9]{3})(-(.*[\r\n]{1,2})+\\1)? [^\r\n]+[\r\n]{1,2}$/', $message) ) $go = false;
      }
    } while($go);

    $this->log($message);

    return $message;
  }

  /**
   * Checks if the response of a command is ok
   *
   * @access private
   * @return Boolean
   */
  private function valid() {
    // get response of the server
    $this->response = $this->getReply();

    // check the response and say if everything is allright
    return (empty($this->response) || preg_match('/^[5]/', $this->response)) ? false : true;
  }

  /**
   * Checks if the response-code is correct
   *
   * @access private
   * @param String $code
   * @return Boolean
   */
  private function check($code) {
    if( $this->valid() ) {
      $pat = '/^'. $code .'/';
      if( preg_match($pat, $this->response)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Saves all request to the server and their responses into $this->log
   *
   * @access private
   * @return void
   */
  private function log($str) {
    $this->log .= "$str<br />";
  }
  
  /**
   * Prints out all requests to the server and their responses
   *
   * @access public
   * @return void
   */
  public function getLog() {
    return $this->log;
  }
}
?>
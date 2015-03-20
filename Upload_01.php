<?php
class Ps2_Upload {
	
  protected $_uploaded = array();
  protected $_destination;
  protected $_max = 51200;
  protected $_messages = array();
  protected $_permitted = array('image/gif',
								'image/jpeg',
								'image/pjpeg',
								'image/png');
  protected $_renamed = false;
  protected $_logfile;

  public function __construct($path) {
	if (!is_dir($path) || !is_writable($path)) {
	  throw new Exception("$path must be a valid, writable directory.");
	}
	$this->_destination = $path;
	$this->_uploaded = $_FILES;
  }

  public function set_logfile($log) {
  	$this->_logfile = $log;
  }

  public function move() {
	$field = current($this->_uploaded);
	$success = move_uploaded_file($field['tmp_name'], $this->_destination . $field['name']);
	if ($success) {
	  $this->_messages[] = $field['name'] . ' uploaded successfully';
	  if (isset($this->_logfile)) {
	  	$log = fopen($this->_logfile, 'a+');
	  	$logstring = date(DATE_RFC1123) . " - " . $this->_destination . $field['name'];
	  	$logstring .= " - " . $_SESSION['role'] . " - " . $_SESSION['username'] . "\n";
  		fwrite($log, $logstring);
  		fclose($log);
	  }
	} else {
	  $this->_messages[] = 'Could not upload ' . $field['name'];
	}
  }

  public function getMessages() {
	return $this->_messages;
  }

}
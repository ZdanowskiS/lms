<?php

class Api {

	private $db;			// database object
	private $AUTH;			// object from Session.class.php (session management)
	private $LMS;

	public function __construct() { // class variables setting
		global $AUTH, $LMS;

		$this->db = LMSDB::getInstance();
		$this->AUTH = $AUTH;
		$this->LMS= $LMS;

		$options = array();
	}

}

?>

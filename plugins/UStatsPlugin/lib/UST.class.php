<?php

class UST {

	private $db;			// database object
	private $AUTH;			
	private $LMS;

	public function __construct() { 
		global $AUTH, $LMS;

		$this->db = LMSDB::getInstance();
		$this->AUTH = $AUTH;
		$this->LMS= $LMS;
		$options = array();
	}

}

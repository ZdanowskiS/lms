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

	public function CustomerDisplayAdd($action,$val)
	{
		$this->db->Execute('INSERT INTO ust_log(action, userid, val, cdate) VALUES(?, ?, ?, ?NOW?)',
							array($action,
									Auth::GetCurrentUser(),
									$val));

		return $this->db->GetLastInsertID('ust_log');
	}

	public function GetLastUserAction()
	{
		$result = $this->db->GetRow('SELECT action, val FROM ust_log WHERE userid=?',
							array(Auth::GetCurrentUser()));
		return $result;
	}
}

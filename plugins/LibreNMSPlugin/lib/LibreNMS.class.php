<?php

class LibreNMS {

	private $DB;			// database object
	public $AUTH;			// object from Session.class.php (session management)
	public $LMS;

    public $connection;

	public function __construct() { // class variables setting
		global $AUTH, $LMS, $SYSLOG;

		$this->db = LMSDB::getInstance();
		$this->AUTH = &$AUTH;
		$this->LMS=  new LibreNMSLMS($this->db,$AUTH,$SYSLOG);
        $this->SESSION = &$SESSION;

        $this->connection= new LibreNMSApi();

    }

    function login()
    {
        $this->AUTH->login=$_SERVER['PHP_AUTH_USER'];
        $this->AUTH->passwd=$_SERVER['PHP_AUTH_PW'];
        $this->AUTH->VerifyUser();
    }

}

?>

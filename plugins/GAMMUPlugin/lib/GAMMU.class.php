<?php

class GAMMU {

    const MYSQL = 'mysql';
    const MYSQLI = 'mysqli';
    const POSTGRESQL = 'postgres';

	private $db;			// database object
	private $AUTH;			// object from Session.class.php (session management)
	private $LMS;
	private $gdb;			// gammu db

	public function __construct() { // class variables setting
		global $AUTH, $LMS;

		$this->db = LMSDB::getInstance();
		$this->AUTH = $AUTH;
		$this->LMS= $LMS;
	}

    public function getDB($dbtype, $dbhost, $dbuser, $dbpasswd, $dbname, $debug = false)
    {
        $dbtype = strtolower($dbtype);

        $db = null;

        switch ($dbtype) {
            case self::MYSQL:
                $db = new LMSDB_driver_mysql($dbhost, $dbuser, $dbpasswd, $dbname);
                break;
            case self::MYSQLI:
                $db = new LMSDB_driver_mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
                break;
            case self::POSTGRESQL:
                $db = new LMSDB_driver_postgres($dbhost, $dbuser, $dbpasswd, $dbname);
                break;
            default:
                throw new Exception('Unable to load driver for "' . $dbtype . '" database!');
        }

        if (!$db->IsLoaded()) {
            return "error";
        }

        if (!$db->GetDbLink()) {
            return "error";
        }

        $db->SetDebug($debug);

        $db->SetEncoding('UTF8');

        return $db;
    }

    public function getInstance()
    {
		if (!is_null($this->gdb)) {
				$this->destroy();
		}
        if ($this->gdb === null) {
            $_DBTYPE = (ConfigHelper::getConfig('gammudb.type') ? ConfigHelper::getConfig('gammudb.type') : 'mysql');
            #$_DBHOST = ConfigHelper::getConfig('iptv-radius.host');
			$_DBHOST = (ConfigHelper::getConfig('gammudb.ip') ? ConfigHelper::getConfig('gammudb.ip') : '127.0.0.1');
            $_DBUSER = (ConfigHelper::getConfig('gammudb.user') ? ConfigHelper::getConfig('gammudb.user') : '');
            $_DBPASS = (ConfigHelper::getConfig('gammudb.password') ? ConfigHelper::getConfig('gammudb.password') : '');
            $_DBNAME = ConfigHelper::getConfig('gammudb.database');
            $_DBDEBUG = false;
            if (LMSConfig::getIniConfig()->getSection('database')->hasVariable('debug')) {
                $_DBDEBUG = ConfigHelper::checkValue(LMSConfig::getIniConfig()->getSection('database')->getVariable('debug')->getValue());
            }
            $this->gdb = $this->getDB($_DBTYPE, $_DBHOST, $_DBUSER, $_DBPASS, $_DBNAME, $_DBDEBUG);

        }
    }

	public function destroy()
	{
		$this->gdb=null;
	}

	public function GetInboxList($order = 'name,asc', $limit = null, $offset = null, $count = false)
	{
		$this->getInstance();
		if(!is_object($this->gdb)){
			return;	
		}

		list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction != 'desc') ? $direction = 'asc' : $direction = 'desc';

        switch ($order) {
            case 'sendernumber':
                $sqlord = ' ORDER BY SenderNumber';
                break;
            case 'recivingdatetime':
                $sqlord = ' ORDER BY ReceivingDateTime';
                break;
            case 'updateindb':
                $sqlord = ' ORDER BY UpdatedInDB';
                break;
            default:
                $sqlord = ' ORDER BY id';
                break;
		}

		$sql = '';
		if($count) {
			$sql .= 'SELECT COUNT(*) ';
		}else {
			$sql .= 'SELECT UpdatedInDB,  ReceivingDateTime, Text, SenderNumber, Coding, UDH, SMSCNumber, 
						Class, TextDecoded, ID, RecipientID, Processed, Status ';
		}
		$sql .= ' FROM inbox  WHERE 1=1 '
					.($sqlord != ''  && !$count ? $sqlord . ' ' . $direction : '')
                	.($limit !== null && !$count ? ' LIMIT ' . $limit : '')
                	.($offset !== null && !$count ? ' OFFSET ' . $offset : '');

		if (!$count) {
			$inboxlist = $this->gdb->GetAll($sql);

			$inboxlist['total'] = count($inboxlist);
			$inboxlist['order'] = $order;
			$inboxlist['direction'] = $direction;

			return $inboxlist;
		}else {
			return $this->gdb->getOne($sql);
		}
		return;
	}

	public function GetInbox($id)
	{
		$this->getInstance();
		if(!is_object($this->gdb)){
			return;	
		}

		return $this->gdb->GetRow('SELECT UpdatedInDB,  ReceivingDateTime, Text, SenderNumber, Coding, UDH, SMSCNumber, 
						Class, TextDecoded, ID, RecipientID, Processed, Status 
							FROM inbox 
							WHERE ID=?',
							array($id));
	}

	public function InboxExists($id)
	{
		$this->getInstance();

		if(!is_object($this->gdb)){
			return;	
		}

		return ($this->gdb->GetOne('SELECT id FROM inbox WHERE id=?',array($id)) ? TRUE : FALSE);
	}

####OUTBOX
	public function GetOutboxList($order = 'name,asc', $limit = null, $offset = null, $count = false)
	{
		$this->getInstance();
		if(!is_object($this->gdb)){
			return;	
		}

		list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction != 'desc') ? $direction = 'asc' : $direction = 'desc';

        switch ($order) {
            case 'sendernumber':
                $sqlord = ' ORDER BY SenderNumber';
                break;
            case 'senderid':
                $sqlord = ' ORDER BY SenderID';
                break;
            case 'status':
                $sqlord = ' ORDER BY Status';
                break;
            default:
                $sqlord = ' ORDER BY id';
                break;
		}

		$sql = '';
		if($count) {
			$sql .= 'SELECT COUNT(*) ';
		}else {
			$sql .= 'SELECT UpdatedInDB, InsertIntoDB, SendingDateTime, SendBefore, SendAfter, Text,
								DestinationNumber, Coding, UDH, Class, TextDecoded, ID, MultiPart, RelativeValidity,
								SenderID, SendingTimeout, DeliveryReport, CreatorID, Retries, Priority, Status,
								StatusCode ';
		}
		$sql .= 'FROM outbox WHERE 1=1'
					.($sqlord != ''  && !$count ? $sqlord . ' ' . $direction : '')
                	.($limit !== null && !$count ? ' LIMIT ' . $limit : '')
                	.($offset !== null && !$count ? ' OFFSET ' . $offset : '');

		if (!$count) {
			$inboxlist = $this->gdb->GetAll($sql);

			$inboxlist['total'] = count($inboxlist);
			$inboxlist['order'] = $order;
			$inboxlist['direction'] = $direction;

			return $inboxlist;
		}else {
			return $this->gdb->getOne($sql);
		}
		return;
	}

	public function GetOutbox($id)
	{
		$this->getInstance();
		if(!is_object($this->gdb)){
			return;	
		}

		return $this->gdb->GetRow('SELECT UpdatedInDB, InsertIntoDB, SendingDateTime, SendBefore, SendAfter, Text,
								DestinationNumber, Coding, UDH, Class, TextDecoded, ID, MultiPart, RelativeValidity,
								SenderID, SendingTimeout, DeliveryReport, CreatorID, Retries, Priority, Status,
								StatusCode 
							FROM outbox 
							WHERE ID=?',
							array($id));
	}

	public function OutboxExists($id)
	{
		$this->getInstance();

		if(!is_object($this->gdb)){
			return;	
		}

		return ($this->gdb->GetOne('SELECT id FROM outbox WHERE id=?',array($id)) ? TRUE : FALSE);
	}

####Phones
	public function GetPhonesList($order = 'netname,asc', $limit = null, $offset = null, $count = false)
	{
		$this->getInstance();
		if(!is_object($this->gdb)){
			return;	
		}

		list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction != 'desc') ? $direction = 'asc' : $direction = 'desc';

        switch ($order) {
            case 'SenderNumber':
                $sqlord = ' ORDER BY SenderNumber';
                break;
            default:
                $sqlord = ' ORDER BY NetNames';
                break;
		}

		$sql = '';
		if($count) {
			$sql .= 'SELECT COUNT(*) ';
		}else {
			$sql .= 'SELECT ID, UpdateInDB, InsertIntoDB, Timeout, Send, Recive, IMEI, IMSI, NetCode, NetName, Client,
								Battery, Signal, Sent, received';
		}
		$sql .= 'FROM phones WHERE 1=1';

		if (!$count) {
			$inboxlist = $this->gdb->GetAll($sql);

			$inboxlist['total'] = count($inboxlist);
			$inboxlist['order'] = $order;
			$inboxlist['direction'] = $direction;

			return $inboxlist;
		}else {
			return $this->gdb->getOne($sql);
		}
		return;
	}

	public function GetPhones($id)
	{
		$this->getInstance();
		if(!is_object($this->gdb)){
			return;	
		}

		$result = $this->gdb->getRow('SELECT ID, UpdateInDB, InsertIntoDB, Timeout, Send, Recive, IMEI, IMSI, 
							NetCode, NetName, Client,Battery, Signal, Sent, received 
							FROM phones WHERE ID=?',array($id));
		return $result;
	}

	public function SendSms()
	{
				$DBGAMMU = DBInit($this->CONFIG['sms']['dbtype'], $this->CONFIG['sms']['dbhost'], $this->CONFIG['sms']['dbuser'], 
$this->CONFIG['sms']['dbpass'], $this->CONFIG['sms']['dbname']);

				if(!$DBGAMMU)
				{
					print " can't working without database";
					die();
				}

				$DBGAMMU->execute('INSERT INTO outbox (
    							DestinationNumber,
    							TextDecoded,
    							CreatorID,
    							Coding,
								DeliveryReport
								) VALUES (?, ?, ?, ?, ?)',
								array(
    							$number,
    							"$message",
    							$this->CONFIG['sms']['senderid'],
    							"Default_No_Compression",
								"yes"
								));


				$gammuid=$DBGAMMU->GetOne('SELECT MAX(ID) FROM outbox');

				$this->DB->Execute('INSERT INTO messagegammu(id, gammuid) VALUE(?, ?)',
									array($messageid,
											$gammuid));
	}
}
?>

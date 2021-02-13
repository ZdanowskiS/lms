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

	public function GetInboxList($order = 'name,asc', $datefrom, $dateto, $limit = null, $offset = null, $count = false)
	{
        global $DB, $LMS;
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
                    .($datefrom ? ' AND unix_timestamp(ReceivingDateTime)>'.$datefrom : '')
                    .($dateto ? ' AND unix_timestamp(ReceivingDateTime)<'.$dateto : '')
					.($sqlord != ''  && !$count ? $sqlord . ' ' . $direction : '')
                	.($limit !== null && !$count ? ' LIMIT ' . $limit : '')
                	.($offset !== null && !$count ? ' OFFSET ' . $offset : '');

		if (!$count) {
			$inboxlist = $this->gdb->GetAll($sql);

            foreach($inboxlist as $k => $sms)
            {
                $shortnumber=null;
                
                if(strpos($sms['SenderNumber'],'+')!==false)
                    $shortnumber=substr($sms['SenderNumber'], 3);

                $inboxlist[$k]['customerid']=$DB->GetOne('SELECT customerid FROM customercontacts 
                                                       WHERE contact='.$DB->Escape($sms['SenderNumber'])
                                                       .($shortnumber ? ' OR contact='.$DB->Escape($shortnumber) : ''));
                if($inboxlist[$k]['customerid'])
                    $inboxlist[$k]['customer']=$LMS->getCustomerName($inboxlist[$k]['customerid']);
            } 

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
        global $DB, $LMS;

		$this->getInstance();
		if(!is_object($this->gdb)){
			return;	
		}

		$result = $this->gdb->GetRow('SELECT UpdatedInDB,  ReceivingDateTime, Text, SenderNumber, Coding, UDH, SMSCNumber, 
						Class, TextDecoded, ID, RecipientID, Processed, Status 
							FROM inbox 
							WHERE ID=?',
							array($id));

        $shortnumber=null;
                
        if(strpos($result['SenderNumber'],'+')!==false)
            $shortnumber=substr($result['SenderNumber'], 3);
        elseif(strlen($sms['SenderNumber'])==11)
            $shortnumber=substr($sms['SenderNumber'], 2);

        $result['customerid']=$DB->GetOne('SELECT customerid FROM customercontacts 
                                                       WHERE contact='.$DB->Escape($result['SenderNumber'])
                                                       .($shortnumber ? ' OR contact='.$DB->Escape($shortnumber) : ''));
        if($result['customerid'])
            $result['customer']=$LMS->getCustomerName($result['customerid']);

        return $result;
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
######
	public function GetSentList($order = 'name,asc', $limit = null, $offset = null, $count = false)
	{
        global $DB, $LMS;
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
			$sql .= 'SELECT UpdatedInDB, InsertIntoDB, SendingDateTime, Text,
								DestinationNumber, Coding, UDH, Class, TextDecoded, ID, RelativeValidity,
								SenderID, CreatorID, Status,
								StatusCode ';
		}
		$sql .= 'FROM sentitems WHERE 1=1'
                    .($datefrom ? ' AND unix_timestamp(InsertIntoDB)>'.$datefrom : '')
                    .($dateto ? ' AND unix_timestamp(InsertIntoDB)<'.$dateto : '')
					.($sqlord != ''  && !$count ? $sqlord . ' ' . $direction : '')
                	.($limit !== null && !$count ? ' LIMIT ' . $limit : '')
                	.($offset !== null && !$count ? ' OFFSET ' . $offset : '');

		if (!$count) {
			$sentlist = $this->gdb->GetAll($sql);

            foreach($sentlist as $k => $sms)
            {
                $shortnumber=null;
                
                if(strpos($sms['DestinationNumber'],'+')!==false)
                    $shortnumber=substr($sms['DestinationNumber'], 3);
                elseif(strlen($sms['DestinationNumber'])==11)
                    $shortnumber=substr($sms['DestinationNumber'], 2);

                $sentlist[$k]['customerid']=$DB->GetOne('SELECT customerid FROM customercontacts 
                                                       WHERE contact='.$DB->Escape($sms['DestinationNumber'])
                                                       .($shortnumber ? ' OR contact='.$DB->Escape($shortnumber) : ''));
                if($sentlist[$k]['customerid'])
                    $sentlist[$k]['customer']=$LMS->getCustomerName($sentlist[$k]['customerid']);
            } 

#print_r($sentlist);
			$sentlist['total'] = count($sentlist);
			$sentlist['order'] = $order;
			$sentlist['direction'] = $direction;

			return $sentlist;
		}else {
			return $this->gdb->getOne($sql);
		}
		return;
	}

	public function GetSent($id)
	{
        global $DB, $LMS;

		$this->getInstance();
		if(!is_object($this->gdb)){
			return;	
		}

		$result = $this->gdb->GetRow('SELECT UpdatedInDB,  SendingDateTime, Text, DestinationNumber, Coding, UDH, SMSCNumber, 
                                Class, TextDecoded, ID, CreatorID, Status FROM sentitems 
							        WHERE ID=?',
							        array($id));

        $shortnumber=null;
                
        if(strpos($result['DestinationNumber'],'+')!==false)
            $shortnumber=substr($result['DestinationNumber'], 3);
        elseif(strlen($result['DestinationNumber'])==11)
            $shortnumber=substr($result['DestinationNumber'], 2);

        $result['customerid']=$DB->GetOne('SELECT customerid FROM customercontacts 
                                                       WHERE contact='.$DB->Escape($result['DestinationNumber'])
                                                       .($shortnumber ? ' OR contact='.$DB->Escape($shortnumber) : ''));
        if($result['customerid'])
            $result['customer']=$LMS->getCustomerName($result['customerid']);

        return $result;
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

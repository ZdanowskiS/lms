<?php

class PROXMOX {

	private $node;
	private $host;
	private $login;
	private $realm;
	private $passward;
	private $port;
	private $verify_ssl;
	private $ticket;

	public $connection;

	public function __construct() { // class variables setting
		global $AUTH,$LMS;

		$this->db = LMSDB::getInstance();
		$this->AUTH = $AUTH;
		$this->LMS = $LMS;

        $this->connection= new ProxmoxApi();

		$options = array();
	}

	public function nodeAdd($data)
	{

		$this->db->Execute('INSERT INTO proxmox_nodes(name, ipaddr, realm, login, password) 
								VALUES(?, inet_aton(?), ?, ?, ?)',
							array($data['name'],
									$data['ipaddr'],
									$data['realm'],
									$data['login'],
									$data['password']));

		return $this->db->GetLastInsertID('proxmox_nodes');
	}

	public function NodeExists($id)
	{
		return ($this->db->GetOne('SELECT id FROM proxmox_nodes WHERE id=?',array($id)) ? TRUE : FALSE);
	}
	
    public function NodeNameExists($name)
    {
        return ($this->db->GetOne('SELECT id FROM proxmox_nodes WHERE name ?LIKE? '.$this->db->Escape($name)) ? TRUE : FALSE);    
    }

	public function GetNode($id)
	{
		$result = $this->db->GetRow('SELECT id, name, inet_ntoa(ipaddr) as ipaddr, realm, login, password 
									FROM proxmox_nodes WHERE id=?',
									array($id));
		return $result;
	}

	public function GetNodeNames()
	{
		$result= $this->db->GetAllByKey('SELECT id, name FROM proxmox_nodes ORDER BY name', 'id');

		return $result;
	}

	public function NodeUpdate($data)
	{
		$this->db->Execute('UPDATE proxmox_nodes SET name=?, ipaddr=inet_aton(?), realm=?, login=?, password=? 
								WHERE id=?',
							array($data['name'],
									$data['ipaddr'],
									$data['realm'],
									$data['login'],
									$data['password'],
									$data['id']));
		return;
	}

	public function DeleteNode($id)
	{
		$this->db->Execute('DELETE FROM proxmox_nodes WHERE id=?',
							array($id));
		return;
	}

	public function GetNodeList($order = 'name,asc', $limit = null, $offset = null, $count = false)
	{
        if ($order == '')
            $order = 'name,asc';

        list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction == 'desc') ? $direction = 'desc' : $direction = 'asc';

        switch ($order) {
            case 'ipaddr':
                $sqlord = ' ORDER BY ipaddr';
                break;
			default:
				$sqlord = ' ORDER BY name';
        }

		$sql='SELECT id, name, inet_ntoa(ipaddr) as ipaddr, realm, login, password 
						 FROM proxmox_nodes'
					.($sqlord != ''  && !$count ? $sqlord . ' ' . $direction : '')
					.($limit !== null && !$count ? ' LIMIT ' . $limit : '')
					.($offset !== null && !$count ? ' OFFSET ' . $offset : '');

		if (!$count) {
			$modelslist = $this->db->GetAll($sql);

            $modelslist['order'] = $order;
            $modelslist['direction'] = $direction;

			return $modelslist;
		}else {
			return $this->db->getOne($sql);
		}

		return $modelslist;	
	}

    public function BridgeAdd($data)
    {
        $this->db->Execute('INSERT INTO proxmox_node_bridges(nodeid, name) VALUES(?, ?)',
                        array($data['nodeid'],$data['name']));

		return $this->db->GetLastInsertID('proxmox_node_bridges');
    }

    public function GetNodeBridges($nodeid)
    {
        $result =$this->db->GetAll('SELECT id, nodeid, name FROM proxmox_node_bridges 
                                WHERE nodeid=?',
                           array($nodeid));
        return $result;
    }

    public function GetBridges()
    {
        $result =$this->db->GetAllByKey('SELECT id, nodeid, name FROM proxmox_node_bridges','id');
        return $result;
    }

    public function StorageAdd($data)
    {
        $this->db->Execute('INSERT INTO proxmox_node_storages(nodeid, name) VALUES(?, ?)',
                        array($data['nodeid'],$data['name']));

		return $this->db->GetLastInsertID('proxmox_node_storages');
    }

    public function GetNodeStorages($nodeid)
    {
        $result =$this->db->GetAll('SELECT id, nodeid, name FROM proxmox_node_storages 
                                WHERE nodeid=?',
                           array($nodeid));
        return $result;
    }

    public function GetStorage()
    {
        $result =$this->db->GetAllByKey('SELECT id, nodeid, name FROM proxmox_node_storages','id');

        return $result;
    }

    public function VZTemplateAdd($data)
    {
        $this->db->Execute('INSERT INTO proxmox_node_vztemplates(nodeid, name) VALUES(?, ?)',
                           array($data['nodeid'],$data['name']));

		return $this->db->GetLastInsertID('proxmox_node_vztemplates');
    }

    public function GetNodeVZTemplates($nodeid)
    {
        $result =$this->db->GetAll('SELECT id, nodeid, name FROM proxmox_node_vztemplates 
                                WHERE nodeid=?',
                           array($nodeid));

        return $result;
    }

    public function GetVZTemplates()
    {
        $result =$this->db->GetAllByKey('SELECT id, nodeid, name FROM proxmox_node_vztemplates','id');

        return $result;
    }

	public function OfferAdd($data)
	{
		$this->db->Execute('INSERT INTO proxmox_offerts(name, type, nodeid, clone, cores, memory, storageid, 
								size, vztemplateid, net, nettype, bridgeid, ratelimit) 
							VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
							array($data['name'],
									$data['type'],
									$data['nodeid'],
                                    ($data['clone'] ? $data['clone'] : null),
									($data['cores'] ? $data['cores'] : 0),
									($data['memory'] ? $data['memory'] : 0),
									$data['storage'],
									($data['size'] ? $data['size'] : 0),
									($data['vztemplateid'] ? $data['vztemplateid'] : null),
									$data['net'],
									$data['nettype'],
									$data['bridgeid'],
									$data['ratelimit']));

		return $this->db->GetLastInsertID('proxmox_offerts');
	}

	public function OfferExists($id)
	{
		return ($this->db->GetOne('SELECT id FROM proxmox_offerts WHERE id=?',array($id)) ? TRUE : FALSE);
	}

	public function GetOffer($id)
	{

		$result = $this->db->GetRow('SELECT o.id, o.name, o.type, o.clone, o.nodeid, o.clone, o.cores, 
                                o.memory, o.storageid, o.size, o.vztemplateid, o.net, o.nettype, o.bridgeid, o.ratelimit,
                                    s.name as storage, v.name as ostemplate, b.name as bridge
									FROM proxmox_offerts o
                                            LEFT JOIN proxmox_node_storages s ON (s.id=o.storageid)
                                            LEFT JOIN proxmox_node_vztemplates v ON (v.id=o.vztemplateid)
                                            LEFT JOIN proxmox_node_bridges b ON (b.id=o.bridgeid)
										WHERE o.id=?',
									array($id));
		return $result;
	}

	public function OfferUpdate($data)
	{
		$this->db->Execute('UPDATE proxmox_offerts SET name=?, type=?, nodeid=?, clone=?, cores=?, memory=?, storageid=?, 
								size=?, vztemplateid=?, net=?, nettype=?, bridgeid=?, ratelimit=? 
								WHERE id=?',
							array($data['name'],
									$data['type'],
									$data['nodeid'],
                                    ($data['clone'] ? $data['clone'] : null),
									($data['cores'] ? $data['cores'] : 0),
									($data['memory'] ? $data['memory'] : 0),
									$data['storage'],
									($data['size'] ? $data['size'] : 0),
									($data['vztemplateid'] ? $data['vztemplateid'] : null),
									$data['net'],
									$data['nettype'],
									$data['bridgeid'],
									$data['ratelimit'],
									$data['id']));

		return;
	}

	public function DeleteOffer($id)
	{
		$this->db->Execute('DELETE FROM proxmox_offerts WHERE id=?',
							array($id));
		return;
	}

	public function GetOfferList($order = 'name,asc', $limit = null, $offset = null, $count = false)
	{
        if ($order == '')
            $order = 'name,asc';

        list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction == 'desc') ? $direction = 'desc' : $direction = 'asc';

        switch ($order) {
            case 'ipaddr':
                $sqlord = ' ORDER BY ipaddr';
                break;
			default:
				$sqlord = ' ORDER BY name';
        }

		$sql='SELECT o.id, o.name, o.cores, o.memory, o.storageid, o.size, o.vztemplateid,
						n.name as nodename, s.name as storagename, v.name as ostemplate, b.name as brigdename   
						 FROM proxmox_offerts o 
							JOIN proxmox_nodes n ON (n.id=o.nodeid)
                                LEFT JOIN proxmox_node_storages s ON (s.id=o.storageid)
                                LEFT JOIN proxmox_node_vztemplates v ON (v.id=o.vztemplateid)
                                LEFT JOIN proxmox_node_bridges b ON (b.id=o.bridgeid)'
					.($sqlord != ''  && !$count ? $sqlord . ' ' . $direction : '')
					.($limit !== null && !$count ? ' LIMIT ' . $limit : '')
					.($offset !== null && !$count ? ' OFFSET ' . $offset : '');

		if (!$count) {
			$offerlist = $this->db->GetAll($sql);

            $offerlist['order'] = $order;
            $offerlist['direction'] = $direction;

			return $offerlist;
		}else {
			return $this->db->getOne($sql);
		}

		return $offerlist;	
	}

	public function GetVMCTList($order = 'name,asc', $limit = null, $offset = null, $count = false)
	{
        if ($order == '')
            $order = 'name,asc';

        list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction == 'desc') ? $direction = 'desc' : $direction = 'asc';

        switch ($order) {
            case 'ipaddr':
                $sqlord = ' ORDER BY ipaddr';
                break;
			default:
				$sqlord = ' ORDER BY name';
        }

		$sql='SELECT v.id, v.name, v.password, v.customerid, v.ipaddr, v.nodeid, v.vmid, v.type, v.cdate,'   
            . $this->db->Concat('c.lastname', "' '", 'c.name') . ' AS owner, n.name as nodename, n.id as nodeid   
						 FROM proxmox_vmct v 
                            JOIN proxmox_nodes n ON (n.id=v.nodeid)
                            JOIN customerview c ON (v.customerid = c.id)'
					.($sqlord != ''  && !$count ? $sqlord . ' ' . $direction : '')
					.($limit !== null && !$count ? ' LIMIT ' . $limit : '')
					.($offset !== null && !$count ? ' OFFSET ' . $offset : '');

		if (!$count) {
			$vmctlist = $this->db->GetAll($sql);

            $vmctlist['order'] = $order;
            $vmctlist['direction'] = $direction;

			return $vmctlist;
		}else {
			return $this->db->getOne($sql);
		}

		return $offerlist;	
	}

    public function GetCustomerVMCT($id)
    {
        $result=$this->db->GetAll('SELECT v.id, v.name, v.nodeid, v.type, v.password, v.customerid, v.ipaddr, v.vmid, v.cdate,
                                    n.name as nodename 
                                    FROM proxmox_vmct v
                                        JOIN proxmox_nodes n ON (n.id=v.nodeid)
                                   WHERE customerid=?',
                                   array($id));

		return $result;
    }

    public function DeleteVMCT($id)
    {
        $this->db->Execute('DELETE FROM proxmox_vmct WHERE id=?',
                            array($id));

        return;
    }

	public function GetOfferNames()
	{
		$result= $this->db->GetAllByKey('SELECT id, name FROM proxmox_offerts ORDER BY name', 'id');

		return $result;
	}

	public function VMCTAdd($data)
	{
		$this->db->Execute('INSERT INTO proxmox_vmct(name, password, customerid, ipaddr, nodeid, vmid, type, cdate) 
							VALUES(?, ?, ?, inet_aton(?), ?, ?, ?, ?NOW?)',
							array($data['name'],
                                    $data['passwd'],
                                    $data['customerid'],
                                    $data['ipaddr'],
									$data['nodeid'],
									$data['vmid'],
									$data['type']));

		return $this->db->GetLastInsertID('proxmox_vmct');
	}

	public function VMCTUpdate($data)
	{
		$this->db->Execute('UPDATE proxmox_vmct SET name=?, password=?, customerid=?, ipaddr=?, nodeid=?, vmid=?, type=?  
								WHERE id=?',
							array($data['name'],
									$data['password'],
									$data['customerid'],
									$data['ipaddr'],
									$data['nodeid'],
									$data['vmid'],
									$data['type'],
									$data['id']));

		return;
	}

	public function VMCTExists($id)
	{
		return ($this->db->GetOne('SELECT id FROM proxmox_vmct WHERE id=?',array($id)) ? TRUE : FALSE);
	}

	public function GetVMCT($id)
	{
		$result = $this->db->GetRow('SELECT v.id, v.name, v.password, v.customerid, inet_ntoa(v.ipaddr) as ipaddr,
                                         v.vmid, v.nodeid, v.cdate, v.type,
                                        n.name as nodename,'
                                    .$this->db->Concat('c.lastname', "' '", 'c.name') . ' AS customer'
							        .' FROM proxmox_vmct v
                                        JOIN proxmox_nodes n ON (n.id=v.nodeid)
                                        JOIN customerview c ON (v.customerid = c.id)
                                    WHERE v.id=?',
                                    array($id));

		return $result;
	}

	public function parseParam($paramlist,$data)
	{
		if($paramlist)foreach($paramlist as $key =>$param)
		{
			if(preg_match('/%name/',$param) && $data['vmct']['name'])
			{
				$paramlist[$key] = preg_replace('/%name/', $data['vmct']['name'], $param);
			}
			if(preg_match('/%ipaddr/',$param) && $data['vmct']['ipaddr'])
			{
				$paramlist[$key] = preg_replace('/%ipaddr/', $data['vmct']['ipaddr'], $param);
			}
			if(preg_match('/%password/',$param) && $data['vmct']['password'])
			{
				$paramlist[$key] = preg_replace('/%password/', $data['vmct']['password'], $param);
			}

			if(preg_match('/%customer_id/',$param) && $data['customer']['id'])
			{
				$paramlist[$key] = preg_replace('/%customer_id/', $data['customer']['id'], $param);
			}
			if(preg_match('/%customer_name/',$param) && $data['customer']['name'])
			{
				$paramlist[$key] = preg_replace('/%customer_name/', $data['customer']['name'], $param);
			}
			if(preg_match('/%customer_lastname/',$param) && $data['customer']['lastname'])
			{
				$paramlist[$key] = preg_replace('/%customer_lastname/', $data['customer']['lastname'], $param);
			}
		}
		return $paramlist;
	}
}

?>

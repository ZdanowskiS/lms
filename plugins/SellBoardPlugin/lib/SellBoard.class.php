<?php

class SellBoard {

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

#Seller
	public function SellerAdd($data)
	{
		$args = array (
				'name' => $data['name'],
				'phone' => ($data['phone'] ? $data['phone'] : ''),
				'email' => ($data['email'] ? $data['email'] : ''),
				);

		$this->db->Execute('INSERT INTO sellboard_sellers(name, phone, email) 
						VALUES(?, ?, ?)',
						array_values($args));

		$id=$this->db->GetLastInsertID('sellboard_sellers');

		return $id;
	}

	public function SellerUpdate($data)
	{
		$args = array (
				'name' => $data['name'],
				'phone' => ($data['phone'] ? $data['phone'] : ''),
				'email' => ($data['email'] ? $data['email'] : ''),
				'id' => $data['id']
				);

		$this->db->execute('UPDATE sellboard_sellers SET name=?, phone=?, email=?  
							WHERE id=?',
							array_values($args));

		return;
	}

	public function SellerExists($id)
	{
		return ($this->db->GetOne('SELECT id FROM sellboard_sellers
						WHERE id = ? '
                        , array($id)) ? TRUE : FALSE);
	}

	public function GetSeller($id)
	{
		$result = $this->db->GetRow('SELECT id, name, phone, email FROM sellboard_sellers 
									WHERE id=?',array($id));
		return $result;
	}

	public function GetSellerNames()
	{
		$result = $this->db->GetAllByKey('SELECT id, name FROM sellboard_sellers','id');

		return $result;
	}

	public function GetSellerList($order= 'name,asc', $limit = null, $offset = null, $count = false)
	{
        if ($order == '')
            $order = 'name,asc';

        list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction == 'desc') ? $direction = 'desc' : $direction = 'asc';

        switch ($order) {
            case 'email':
                $sqlord = ' ORDER BY email';
                break;
            case 'phone':
                $sqlord = ' ORDER BY phone';
                break;
			default:
				$sqlord = ' ORDER BY name';
				
        }

		$sql = '';
		if($count) {
			$sql .= 'SELECT COUNT(id) ';
		}else {
				$sql .= 'SELECT id, name, email, phone ';
		}

		$sql .= 'FROM sellboard_sellers WHERE 1=1'; 

		if (!$count) {
			$sellerlist = $this->db->GetAll($sql);

			$sellerlist['order'] = $order;
			$sellerlist['direction'] = $direction;

			return $sellerlist;
		}else {
			return $this->db->getOne($sql);
		}

	}

#Host
	public function HostAdd($data)
	{
		$args = array (
				'name' => $data['name'],
				'url' => $data['url'],
				'access' => $data['access'],
				'share' => $data['share']
				);

		$this->db->Execute('INSERT INTO sellboard_hosts(name, url, access, share) 
							VALUES(?, ?, ?, ?)',
							array_values($args));

		$id=$this->db->GetLastInsertID('sellboard_hosts');

		return $id;
	}

	public function HostExists($id)
	{
		return ($this->db->GetOne('SELECT id FROM sellboard_hosts
						WHERE id = ? '
                        , array($id)) ? TRUE : FALSE);
	}

	public function GetHost($id)
	{
		$result = $this->db->GetRow('SELECT id, name, url, access, share FROM sellboard_hosts 
									WHERE id=?',array($id));
		return $result;
	}

	public function GetHostList($order= 'name,asc', $limit = null, $offset = null, $count = false)
	{
        if ($order == '')
            $order = 'name,asc';

        list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction == 'desc') ? $direction = 'desc' : $direction = 'asc';

        switch ($order) {
            case 'value':
                $sqlord = ' ORDER BY value';
                break;
            case 'seller':
                $sqlord = ' ORDER BY seller';
                break;
			default:
				$sqlord = ' ORDER BY name';
				
        }

		$sql = '';
		if($count) {
			$sql .= 'SELECT COUNT(id) ';
		}else {
				$sql .= 'SELECT id, name, url, access, share ';
		}

		$sql .= 'FROM sellboard_hosts WHERE 1=1'; 

		if (!$count) {
			$sellerlist = $this->db->GetAll($sql);

			$sellerlist['order'] = $order;
			$sellerlist['direction'] = $direction;

			return $sellerlist;
		}else {
			return $this->db->getOne($sql);
		}
	}

	public function HostUpdate($data)
	{
		$args = array (
				'name' => $data['name'],
				'url' => $data['url'],
				'access' => $data['access'],
				'share' => $data['share'],
				'id' => $data['id']
				);

		$this->db->execute('UPDATE sellboard_hosts SET name=?, url=?, access=?, share=? 
							WHERE id=?',
							array_values($args));
	}

#Category
	public function CategoryAdd($data)
	{
		$args = array (
				'name' => $data['name'],
				);

		$this->db->Execute('INSERT INTO sellboard_category(name) 
						VALUES(?)',
						array_values($args));

		$id=$this->db->GetLastInsertID('sellboard_category');

		return $id;
	}

	public function CategoryUpdate($data)
	{
		$args = array (
				'name' => $data['name'],
				'id' => $data['id']
				);

		$this->db->execute('UPDATE sellboard_category SET name=? WHERE id=?',
							array_values($args));

		return;
	}

	public function GetCategoryList($order= 'name,asc', $limit = null, $offset = null, $count = false)
	{
        if ($order == '')
            $order = 'name,asc';

        list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction == 'desc') ? $direction = 'desc' : $direction = 'asc';

        switch ($order) {
            case 'value':
                $sqlord = ' ORDER BY value';
                break;
            case 'seller':
                $sqlord = ' ORDER BY seller';
                break;
			default:
				$sqlord = ' ORDER BY name';
				
        }

		$sql = '';
		if($count) {
			$sql .= 'SELECT COUNT(id) ';
		}else {
				$sql .= 'SELECT id, name ';
		}

		$sql .= 'FROM sellboard_category WHERE 1=1'; 

		if (!$count) {
			$sellerlist = $this->db->GetAll($sql);

			$sellerlist['order'] = $order;
			$sellerlist['direction'] = $direction;

			return $sellerlist;
		}else {
			return $this->db->getOne($sql);
		}
	}

	public function GetCategory($id)
	{
		$result = $this->db->GetRow('SELECT id, name FROM sellboard_category WHERE id=?',
								array($id));
		return $result;
	}

	public function GetCategoryNames()
	{
		$result = $this->db->GetAllByKey('SELECT id, name FROM sellboard_category','id');

		return $result;
	}

	public function CategoryExists($id)
	{
		return ($this->db->GetOne('SELECT id FROM sellboard_category
						WHERE id = ? '
                        , array($id)) ? TRUE : FALSE);
	}

#item
	public function ItemAdd($data)
	{
		$args = array (
				'name' => $data['name'],
				'description' => $data['description'],
				'value' => $data['value'],
				'ammount' => $data['ammount'],
				'cdate' => time(),
				'sellerid' => $data['sellerid'],	
				'userid' => Auth::GetCurrentUser()		
				);

		$this->db->Execute('INSERT INTO sellboard_items(name, description, value, ammount, cdate, sellerid, userid) 
						VALUES(?, ?, ?, ?, ?, ?, ?)',
						array_values($args));

		$id=$this->db->GetLastInsertID('sellboard_items');

		return $id;
	}

	public function ItemExists($id)
	{
		return ($this->db->GetOne('SELECT id FROM sellboard_items
						WHERE id = ? '
                        , array($id)) ? TRUE : FALSE);
	}

	public function ItemUpdate($data)
	{
		$args = array (
				'name' => $data['name'],
				'description' => $data['description'],
				'value' => $data['value'],
				'ammount' => $data['ammount'],
				'sellerid' => $data['sellerid'],	
				'id' => $data['id']		
				);

		$this->db->Execute('UPDATE sellboard_items SET name=?, description=?, value=?, ammount=?, sellerid=? 
								WHERE id=?',
							array_values($args));

		$this->db->Execute('DELETE FROM sellboard_itemcategories WHERE itemid=?',array($data['id']));

		if($data['category'])foreach($data['category'] as $value)
		{
			$this->db->Execute('INSERT INTO sellboard_itemcategories(itemid, categoryid) 
								VALUES(?, ?)',array($data['id'],$value['categoryid']));
		}
		return;
	}

	public function GetItem($id)
	{
		$result = $this->db->getRow('SELECT si.id, si.name, si.description, si.value, si.ammount, 
										si.cdate, si.sellerid, si.userid, ss.name as sellername  
									FROM sellboard_items si
										JOIN sellboard_sellers ss ON (ss.id=si.sellerid)
									WHERE si.id=?', 
									array($id));

		$result['category']=$this->db->GetAllByKey('SELECT si.categoryid,sc.name 
											FROM sellboard_itemcategories si 
												JOIN sellboard_category sc ON (sc.id=si.categoryid)
										WHERE si.itemid='.$id,
								'categoryid');

		return $result;
	}

	public function GetItemList($order= 'name,asc', $category=null, $valuefrom=null, $valueto=null, $limit = null, $offset = null, $count = false)
	{
        if ($order == '')
            $order = 'name,asc';

        list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction == 'desc') ? $direction = 'desc' : $direction = 'asc';

        switch ($order) {
            case 'ammount':
                $sqlord = ' ORDER BY ammount';
                break;
            case 'value':
                $sqlord = ' ORDER BY value';
                break;
            case 'seller':
                $sqlord = ' ORDER BY ss,name';
                break;
			default:
				$sqlord = ' ORDER BY si.name';
				
        }

		$sql = '';
		if($count) {
			$sql .= 'SELECT COUNT(si.id) ';
		}else {
				$sql .= 'SELECT si.id, si.name, si.description, si.value, si.ammount, si.cdate, si.sellerid, si.userid, 
							ss.name as sellername ';
		}

		$sql .= 'FROM sellboard_items si 
					JOIN sellboard_sellers ss ON (ss.id=si.sellerid) WHERE 1=1'
					.($category ? ' AND (SELECT 1 FROM sellboard_itemcategories sic 
													WHERE sic.itemid=si.id AND sic.categoryid='.$category.')=1' : '')
					.($valuefrom ? ' AND si.value>='.$valuefrom : '')
					.($valueto ? ' AND si.value<='.$valueto : '')
                	. ($sqlord != ''  && !$count ? $sqlord . ' ' . $direction : '')
                	. ($limit !== null && !$count ? ' LIMIT ' . $limit : '')
                	. ($offset !== null && !$count ? ' OFFSET ' . $offset : ''); 

		if (!$count) {
			$sellerlist = $this->db->GetAll($sql);
			$sellerlist['order'] = $order;
			$sellerlist['direction'] = $direction;

			return $sellerlist;
		}else {
			return $this->db->getOne($sql);
		}
	}

	public function HostSetAccess($id, $access=FALSE)
	{
		$this->db->Execute('UPDATE sellboard_hosts SET access=? WHERE id=?',
						array(($access ? 1 : 0),
								$id));
		return;
	}
#Rest
	public function RestGetCategory($host)
	{
		$args = array(
			'sellboard' => 1,
			'data' => BDATA_CATEGORYLIST,
		);

		$encodedargs = array();
		foreach (array_keys($args) as $thiskey)
			array_push($encodedargs, urlencode($thiskey) . "=" . urlencode($args[$thiskey]));
			$encodedargs = implode('&', $encodedargs);

		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $host['url']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedargs);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

    	$result = curl_exec($curl);

		$result = json_decode($result, true);

		return $result;
	}

	public function RestGetItemList($host, $category, $valuefrom, $valueto)
	{
		$args = array(
				'sellboard' => 1,
				'data' => BDATA_ITEMLIST,
				'category' => $category,
				'valuefrom' => $valuefrom,
				'valueto' => $valueto,
			);

		$encodedargs = array();
		foreach (array_keys($args) as $thiskey)
			array_push($encodedargs, urlencode($thiskey) . "=" . urlencode($args[$thiskey]));
			$encodedargs = implode('&', $encodedargs);

		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $host['url']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedargs);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

		$result = curl_exec($curl);
		$result = json_decode($result, true);

		return $result;
	}

	public function RestGetHostList($host)
	{
		$args = array(
			'sellboard' => 1,
			'data' => BDATA_HOSTLIST,
		);

		$encodedargs = array();
		foreach (array_keys($args) as $thiskey)
			array_push($encodedargs, urlencode($thiskey) . "=" . urlencode($args[$thiskey]));
			$encodedargs = implode('&', $encodedargs);

		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $host['url']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedargs);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);

    	$result = curl_exec($curl);

		$result = json_decode($result, true);

		return $result;
	}

	public function RestGetItem($host, $itemid)
	{
		$args = array(
			'sellboard' => 1,
			'data' => BDATA_ITEM,
			'itemid' => $itemid,
		);
		$encodedargs = array();

		foreach (array_keys($args) as $thiskey)
			array_push($encodedargs, urlencode($thiskey) . "=" . urlencode($args[$thiskey]));
				$encodedargs = implode('&', $encodedargs);

		$curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $host['url']);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedargs);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);

    	$result = curl_exec($curl);

		$result=json_decode($result, true);

		return $result;
	}
}
?>

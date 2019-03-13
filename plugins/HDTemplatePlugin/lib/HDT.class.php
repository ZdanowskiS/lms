<?php
#print $this->db->_driver_geterror();
class HDT {

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

#template
	public function AddTemplate($data)
	{
		$this->db->Execute('INSERT INTO hdtemplates(name, template) VALUES(?, ?)',
					array($data['name'],
							$data['template']));

		return $this->db->GetLastInsertID('hdtemplates');
	}

	public function UpdateTemplate($data)
	{
		$this->db->execute('UPDATE hdtemplates SET name=?, template=? WHERE id=?',
						array($data['name'],
							$data['template'],
							$data['id']));

		return;
	}

	public function GetTemplateInfo($id)
	{
		return $this->db->GetRow('SELECT id, name, template FROM hdtemplates WHERE id=?',
						array($id));
	}

	public function GetTemplateNames()
	{
		$result=$this->db->GetAllByKey('SELECT id, name FROM hdtemplates', 'id');

		return $result;
	}

	public function GetTemplate($id,$customerid=NULL)
	{
		$result = $this->db->GetOne('SELECT template FROM hdtemplates WHERE id=?',
								array($id));

		if($customerid)
			$result=$this->parseTemplate($result,$customerid);

		return $result;
	}

	public function TemplateExists($id)
	{
		return ($this->db->GetOne('SELECT id FROM hdtemplates
						WHERE id = ? '
                        , array($id)) ? TRUE : FALSE);
	}

	private function parseTemplate($template, $customerid)
	{
		$customer=$this->LMS->GetCustomer($customerid);

		$template = preg_replace('/%balance/', $customer['balance'], $template);
		$template = preg_replace('/%bankaccount/', $customer['bankaccount'], $template);
		$template = preg_replace('/%customername/', $customer['customername'], $template);

		return $template;
	}

	public function GetTemplateList($order= 'name,asc', $limit = null, $offset = null, $count = false)
	{
        if ($order == '')
            $order = 'name,asc';

        list($order, $direction) = sscanf($order, '%[^,],%s');

        ($direction == 'desc') ? $direction = 'desc' : $direction = 'asc';

        switch ($order) {
            case 'id':
                $sqlord = ' ORDER BY id';
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

		$sql .= 'FROM hdtemplates '
                	. ($sqlord != ''  && !$count ? $sqlord . ' ' . $direction : '')
                	. ($limit !== null && !$count ? ' LIMIT ' . $limit : '')
                	. ($offset !== null && !$count ? ' OFFSET ' . $offset : ''); 

		if (!$count) {
			$templatelist = $this->db->GetAll($sql);
			$templatelist['order'] = $order;
			$templatelist['direction'] = $direction;

			return $templatelist;
		}else {
			return $this->db->getOne($sql);
		}
	}
}

?>

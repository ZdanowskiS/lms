<?php

class LMSTaxxo extends Taxxo {

	private $db;
	private $AUTH;			
	private $LMS;

	public function __construct() { 
		global $AUTH, $LMS;

		$this->db = LMSDB::getInstance();
		$this->AUTH = $AUTH;
		$this->LMS= $LMS;

		$this->url=ConfigHelper::getConfig('taxxo.url');
		$this->api_key=ConfigHelper::getConfig('taxxo.api_key');
	}

///Api Actions
	public function GetApiDocumentBynumber($id)
	{
		$invoice=$this->LMS->GetInvoiceContent($id);

		$number= docnumber(array('number' => $invoice['number'],
										'template' => $invoice['template'],
										'cdate' => $invoice['cdate'],
										'customerid' => $invoice['customerid']));

		$action = '/document/byNumber?number='.$number;

		$result = $this->ApiGET($action);
		return $result;
	}

	public function GetApiDocumentById($id)
	{
		$action = 'document/'.$id;

		$result = $this->ApiGET($action);			

		return $result;
	}

	public function GetApiDocumentsList($y,$m)
	{
		$action='documents'
				.($y ? '?year='.$y : '')
				.($m && $y ? '&month='.$m: '');

		$result = $this->ApiGET($action);

		return $result;
	}

	public function DeleteApiDocumentById($id)
	{
		$document = $this->GetDocument($id);

		$action='document/'.$document['taxxoid'];

		$result=$this->ApiDELETE($action);

		return $result;
	}

	public function GetApiRecentlyAdded()
	{
		$action='documents/recentlyAdded';

		$result = $this->ApiGET($action);

		return $result;
	}

	public function ApiDocumentSend($id)
	{
		$invoice=$this->LMS->GetInvoiceContent($id);

		$data=$this->LMS->GetFinancialDocument($invoice,NULL);

		$filename='invoice'.$id.'.pdf';

		$md5sum =md5($data['data']);
		$notes=(ConfigHelper::checkConfig('taxxo.md5',true) ? $md5sum : '');

		$args['FileDetails'] =array('FileName' =>$filename,
										'ContentType' => 'applicatin/pdf',
										'Content' => base64_encode($data['data']));

		$args['General']=array('DocumentType' => ($invoice['doctype']==DOC_INVOICE ? 'SalesInvoice': 'SalesInvoiceCorrection'),
					'Number' => docnumber(array(
										'number' => $invoice['number'],
										'template' => $invoice['template'],
										'cdate' => $invoice['cdate'],
										'customerid' => $invoice['customerid'])),
					'CorrectedNumber' => $invoice['reference'],
					'DrawnUpDate' => date('c',$invoice['cdate']),
					'SaleEndDate' => date('c',$invoice['sdate']),
					'TotalNet' => $invoice['totalbase'],
					'TotalTax' => $invoice['totaltax'],
					'TotalGross' =>$invoice['total'],
					'notes' => $notes,
					'NotToDigitalization' =>ConfigHelper::getConfig('taxxo.digitalize', true),
					);

		$args['CurrencyDetails']=array('Currency' => 'PLN');

		$args['ContractorDetails']=array('Name' => $invoice['name'],
										'Identifier' => $invoice['ten'],
										'AddressStreet' => $invoice['address'],
										'AddressCity' => $invoice['city'],
										'AddressZipCode' => $invoice['zip'],
										'AddressCountry' => 'Polska',
										'BankAccountNumber' => $result['bankaccounts']
										);

		$args['PaymentDetails']=array('PaymentFormType' =>($invoice['paytype']==1 ? 'Cash' : 'BankTransfer'));

		if($invoice['content'])foreach($invoice['content'] as $key => $content){
			$args['TaxLines'][]=array('OrdinalNumber' =>$key,
										'TaxRate' => $content['taxvalue'],
										'NetValue' => $content['basevalue'],
										'GrossValue' => $content['total']);
		}

		$result = $this->ApiPOST('document',json_encode($args));

		$data=array('status' => $result->{'Status'},
						'errorcode' =>$result->{"ErrorCode"},
						'notdigitalize' => (ConfigHelper::getConfig('taxxo.notdigitalize', true) ? 1 : 0),
						'type' => TAXXO_TYPE_INVOICE,
						'taxxoid' => ($result->{'Data'} ? $result->{'Data'} : 0),
						'docid' => $id,
						'filename' => $filename,
						'md5sum' => $md5sum);

		$this->DocumentAdd($data);

		return;
	}

	public function ApiFileSend($data)
	{
		$md5sum =($data['md5sum'] ? : md5($data['data']));

		$notes=(ConfigHelper::checkConfig('taxxo.md5',true) ? $md5sum : '');

		$args['FileDetails'] =array('FileName' =>$data['filename'],
								'ContentType' => 'applicatin/pdf',
								'Content' => base64_encode($data['content']));

		$args['General']=array('NotToDigitalization' => ConfigHelper::getConfig('taxxo.file_notdigitalize', true),
								'notes' => $notes,
								'DocumentType' => 'PurchaseInvoice');

		$result = $this->ApiPOST('document',json_encode($args));

		$data=array('status' => $result->{'Status'},
						'errorcode' =>$result->{"ErrorCode"},
						'notdigitalize' =>(ConfigHelper::getConfig('taxxo.file_notdigitalize', true) ? 1 : 0),
						'type' => TAXXO_TYPE_FILE,
						'taxxoid' => $result->{'Data'},
						'docid' => $id,
						'filename' => $data['filename'],
						'md5sum' => $md5sum);

		if($data['id']){
			$this->DocumentUpdate($data);
		}
		else{
			$this->DocumentAdd($data);
		}

		return;
	}
/////Send
	public function FileDirSend()
	{
		if ($handle = opendir(ConfigHelper::getConfig('taxxo.dir'))) {
    		while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					
					$content=file_get_contents(ConfigHelper::getConfig('taxxo.dir').'/'.$entry);
					$md5sum=md5($content);

					$notes=(ConfigHelper::checkConfig('taxxo.md5',true) ? $md5sum : '');

					$exists=$this->db->GetRow('SELECT id, status FROM taxxo_documents WHERE md5sum=?',
											array($md5sum));

					if(empty($exists) || $exists['status']>TAXXO_STATUS_SENT)
					{
						$data=array('filename' => $entry,
							'content' => $content,
							'notes' => $notes,
							'md5sum' => $md5sum,
							'id' => $exists['id']);

						$this->ApiFileSend($data);
					}
				}
			}
		}
		return;
	}

	public function InvoiceListSend($datafrom, $datato)
	{
		$invoicelist=$this->db->GetCol('SELECT id FROM documents 
							WHERE cdate>=? AND cdate<=? AND (type = '.DOC_CNOTE.' OR type = '.DOC_INVOICE.')
								AND (SELECT 1 FROM taxxo_documents WHERE docid=documents.id) IS NULL',
							array($datafrom,
									$datato));

		if($invoicelist)foreach($invoicelist as $invoice){
			$this->ApiDocumentSend($invoice);
		}
		return;
	}

/////Content

	public function ContentAdd($data)
	{
		$this->db->Execute('INSERT INTO taxxo_content(tdocid, contractorid, totalnet, totaltax, totalgross) 
						VALUES(?, ?, ?, ?, ?)',
						array($data['tdocid'],
								$data['contractorid'],
								$data['totalnet'],
								$data['totaltax'],
								$data['totalgross']));

		return $this->db->GetLastInsertID('taxxo_content');		
	}

	public function ContentDelete($id)
	{
		$this->db->Execute('DELETE FROM taxxo_content WHERE tdocid=?',array($id));

		return;
	}

	public function ContractorAdd($name)
	{
		$this->db->Execute('INSERT INTO taxxo_contractor(name) VALUES(?)',
						array($name));

		return $this->db->GetLastInsertID('taxxo_contractor');
	}

	public function GetContractorByName($name)
	{
		return $this->db->GetOne('SELECT id FROM taxxo_contractor 
										WHERE UPPER name ?LIKE? UPPER('.$this->db->Escape($name).')');
	}

///Documents
	public function DocumentAdd($data)
	{
		$this->db->Execute('INSERT INTO taxxo_documents(status, errorcode, notdigitalize, type, taxxoid, docid, filename,
								 md5sum, ctime) 
							VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?NOW?)',
						array(($data['status'] ? $data['status'] : 0),
								($data['errorcode'] ? $data['errorcode'] : 0),
								($data['notdigitalize'] ? $data['notdigitalize'] : 0),
								$data['type'],
								($data['taxxoid'] ? $data['taxxoid'] : 0),
								($data['docid'] ? $data['docid'] : 0),
								($data['filename'] ? $data['filename'] : ''),
								($data['md5sum'] ? $data['md5sum'] : '')));

		return $this->db->GetLastInsertID('taxxo_documents');
	}

	public function DocumentUpdate($data)
	{
		$this->db->Execute('UPDATE taxxo_documents SET status=?, errorcode=?, notdigitalize=?, type=?, taxxoid=?, docid=?,
							 filename=?, md5sum=?, utime=?NOW? WHERE id=?',
						array(($data['status'] ? $data['status'] : TAXO_STATUS_ERROR),
								($data['errorcode'] ? $data['errorcode'] : 0),
								($data['notdigitalize'] ? $data['notdigitalize'] : 0),
								$data['type'],
								($data['taxxoid'] ? $data['taxxoid'] : 0),
								($data['docid'] ? $data['docid'] : 0),
								($data['filename'] ? $data['filename'] : ''),
								($data['md5sum'] ? $data['md5sum'] : ''),
								$data['id']));
		return;
	}

	public function UpdateDocumentById($id)
	{
		$document = $this->GetDocument($id);

		$documentapi = $this->GetApiDocumentById($document['taxxoid']);

		$this->db->Execute('DELETE FROM taxxo_content WHERE tdocid=?',array($document['id']));

		$contractorid=$this->GetContractorByName($documentapi->{'Data'}->{'ContractorDetails'}->{'Name'});
				
		if(!$contractorid){
			$contractorid = $this->ContractorAdd($documentapi->{'Data'}->{'ContractorDetails'}->{'Name'});
		}

		$data=array('tdocid' => $document['id'],
								'contractorid' => $contractorid,
								'totalnet' => str_replace(',', '.', $documentapi->{'Data'}->{'General'}->{'TotalNet'}),
								'totaltax' => str_replace(',', '.', $documentapi->{'Data'}->{'General'}->{'TotalTax'}),
								'totalgross' => str_replace(',', '.', $documentapi->{'Data'}->{'General'}->{'TotalGross'}));
		$this->ContentAdd($data);

		$this->db->Execute('UPDATE taxxo_documents SET utime=?NOW? WHERE id=?',
									array($document['id']));
		return;
	}

	public function UpdateDocumentList($from, $to, $type=null)
	{
		$documentlist=$this->db->GetAll('SELECT id, status, taxxoid FROM taxxo_documents 
											WHERE ctime>=? AND ctime<=? '
											.($type ? ' AND type='.$type : '')
											,array($from, $to));

		if($documentlis)foreach($documentlist as $key => $value)
		{
			$document = $this->GetApiDocumentById($value['taxxoid']);
			
			$this->db->Execute('DELETE FROM taxxo_content WHERE tdocid=?',array($value['id']));

			$contractorid=$this->GetContractorByName($document->{'ContractorDetails'}->{'Name'});
				
			if(!$contractorid){
				$contractorid = $this->ContractorAdd($document->{'ContractorDetails'}->{'Name'});
			}

			$data=array('tdocid' => $value['id'],
								'contractorid' => $contractorid,
								'totalnet' =>str_replace(',', '.', $document->{'Data'}->{'General'}->{'TotalNet'}),
								'totaltax' => str_replace(',', '.', $document->{'Data'}->{'General'}->{'TotalTax'}),
								'totalgross' => str_replace(',', '.', $document->{'Data'}->{'General'}->{'TotalGross'}));
			$this->ContentAdd($data);

			$this->db->Execute('UPDATE taxxo_documents SET utime=?NOW? WHERE id=?',
									array($value['id']));
		}
	}

	public function DocumentSetTaxxoId($data)
	{
		$this->db->Execute('UPDATE taxxo_documents SET taxxoid=? WHERE id=?',	
						array($data['taxoid'],
								$data['id']));
		return;
	}

	public function GetDocumentList($order= 'name,asc',$s, $t, $cat, $search, $limit = null, $offset = null, $count = false)
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
				$sqlord = ' ORDER BY filename';
		}

		$where = '';

		if($search!='' && $cat)
		{
			switch($cat)
			{
				case 'cdate':
					$where = ' AND ctime >= '.intval($search).' AND ctime < '.(intval($search)+86400);
					break;
				case 'month':
					$last = mktime(23,59,59, date('n', $search) + 1, 0, date('Y', $search));
					$where = ' AND ctime >= '.intval($search).' AND ctime <= '.$last;
					break;
				case 'contractor':
					$where = ' AND (SELECT 1 FROM taxxo_contractor con 
												JOIN taxxo_content c ON (c.contractorid=con.id)
									WHERE c.tdocid=d.id AND con.name ?LIKE? ' 
								. $this->db->Escape($search.'%').') IS NOT NULL';
					break;
			}
		}
		$sql = '';
		if($count) {
			$sql .= 'SELECT COUNT(id) ';
		}else {
			$sql .= 'SELECT d.id, d.status, d.errorcode, d.notdigitalize, d.type, d.taxxoid, d.docid, d.filename,
								 d.md5sum, d.ctime, d.utime ';
		}
		$sql .= ' FROM taxxo_documents d 
					WHERE 1=1'
					.($s ? ' AND d.status='.$s : '')
					.($t ? ' AND d.type='.$t : '')
					.$where; 

		if (!$count) {
			$documentlist = $this->db->GetAll($sql);
			return $documentlist;
		}else {
			return $this->db->getOne($sql);
		}
	}

	public function DocumentExists($id)
	{
		return ($this->db->GetOne('SELECT id FROM taxxo_documents 
						WHERE id = ? '
                        , array($id)) ? TRUE : FALSE);
	}

	public function GetDocument($id)
	{
		$result = $this->db->GetRow('SELECT id, status, errorcode, notdigitalize, type, taxxoid, docid, 
							filename, md5sum, ctime, utime 
							FROM taxxo_documents 
								WHERE id=?',array($id));
		return $result;
	}

	public function DocumentDelete($id)
	{
		$this->ContentDelete($id);
		$this->db->Execute('DELETE FROM taxxo_documents WHERE id=?',array($id));

		return;
	}
}

?>

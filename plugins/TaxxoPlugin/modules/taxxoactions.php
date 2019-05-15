<?php

$TAXXO = TaxxoPlugin::getTaxxoInstance();

$action = isset($_GET['action']) ? $_GET['action'] : '';

if($action=='send')
{
	$datefrom  = !empty($_GET['datefrom']) ? $_GET['datefrom'] : $_POST['datefrom'];
	$dateto  = !empty($_GET['dateto']) ? $_GET['dateto'] : $_POST['dateto'];

	if($datefrom ) {
		list($year, $month, $day) = explode('/',$datefrom );
		$unixfrom = mktime(0,0,0,$month,$day,$year);
	} else {
		$from = date('Y/m/d',time());
		$unixfrom = mktime(0,0,0); //today
	}

	if($dateto) {
		list($year, $month, $day) = explode('/',$dateto);
		$unixto = mktime(23,59,59,$month,$day,$year);
	} else {
		$dateto = date('Y/m/d',time());
		list($year, $month, $day) = explode('/',$dateto);
		$unixto = mktime(23,59,59,$month,$day,$year);
	}

	$TAXXO->ApiAuthenticate();

	$TAXXO->InvoiceListSend($unixfrom, $unixto);

	$TAXXO->ApiLogout();
}
elseif($action=='sendfiles')
{
	if(!ConfigHelper::getConfig('taxxo.dir')){
		$SMARTY->assign('body','First set taxxo.dir');
		$SMARTY->display('dialog.html');
		exit();
	}
	$TAXXO->ApiAuthenticate();

	$TAXXO->FileDirSend();

	$TAXXO->ApiLogout();
}
elseif($action=='updatedocuments')
{
	$datefrom  = !empty($_GET['datefrom']) ? $_GET['datefrom'] : $_POST['datefrom'];
	$dateto  = !empty($_GET['dateto']) ? $_GET['dateto'] : $_POST['dateto'];
	$type  = !empty($_GET['type']) ? $_GET['type'] : $_POST['type'];

	if($datefrom ) {
		list($year, $month, $day) = explode('/',$datefrom );
		$unixfrom = mktime(0,0,0,$month,$day,$year);
	} else {
		$from = date('Y/m/d',time());
		$unixfrom = mktime(0,0,0); //today
	}

	if($dateto) {
		list($year, $month, $day) = explode('/',$dateto);
		$unixto = mktime(23,59,59,$month,$day,$year);
	} else {
		$dateto = date('Y/m/d',time());
		list($year, $month, $day) = explode('/',$dateto);
		$unixto = mktime(23,59,59,$month,$day,$year);
	}

	$TAXXO->ApiAuthenticate();

	$TAXXO->UpdateDocumentList($unixfrom, $unixto, $type);

	$TAXXO->ApiLogout();
}
elseif($action=='verify')
{
	$TAXXO->ApiAuthenticate();

	$result=$TAXXO->GetApiRecentlyAdded();

	$TAXXO->ApiLogout();

	$date = strtotime(date("Y-m-d", strtotime("-7 day")));

	if(ConfigHelper::checkConfig('taxxo.md5',true))
	{
		$documentslist = $DB->GetAllByKey('SELECT id, taxxoid, docid, filename, md5sum, ctime  
										FROM taxxo_documents WHERE ctime>=?','md5sum',
										array($date));

		if($result->{'Data'})foreach($result->{'Data'} as $data)
		{	
			if(($documentslist[$data->{'General'}->{'Notes'}])){
				$documentslist[$data->{'General'}->{'Notes'}]['remoteid']=$data->{'DocumentId'};

				if(!$documentslist[$data->{'General'}->{'Notes'}]['taxxoid']){
					$DB->Execute('UPDATE taxxo_documents SET taxxoid=? WHERE id=?',
									array($data->{'DocumentId'},
											$documentslist[$data->{'General'}->{'Notes'}]['id']));
				}
			}
			else{
				$error[]=array('Date' => $data->{'StatusDetails'}->{'AcceptanceDate'},
								'Status' => $data->{'StatusDetails'}->{'DocumentStatus'},
								'Number' => $data->{'General'}->{'Number'},
								'filename' => $documentslist[$data->{'General'}->{'Notes'}]['filename'],
								'Error' => trans('not found in local files'));
					
			}
		}
	}
	else{
		$documentslist = $DB->GetAll('SELECT id, taxxoid, docid, filename, md5sum, ctime  
										FROM taxxo_documents WHERE ctime>=?',
										array($date));

		if($result->{'Data'})foreach($result->{'Data'} as $data)
		{	
			$found=0;
			if($documentslist)foreach($documentslist as $key => $doc){
				if($doc['taxxoid']==$data->{'DocumentId'}){
					$documentslist[$key]['remoteid']=$data->{'DocumentId'};
					$found=1;
				}
			}
			if(!$found){
				$error[]=array('Date' => $data->{'StatusDetails'}->{'AcceptanceDate'},
								'Status' => $data->{'StatusDetails'}->{'DocumentStatus'},
								'Number' => $data->{'General'}->{'Number'},
								'filename' => $documentslist[$data->{'General'}->{'Notes'}]['filename'],
								'Error' => trans('Not found in local files'));
			}
		}
	}

	if($documentslist)foreach($documentslist as $key => $document){
		if(!$document['remoteid']){
			$error[]=array('Date' => $document['ctime'],
								'Status' => '',
								'Number' => '',
								'filename' => $document['filename'],
								'Error' => trans('Not found in TAXXO files'));
		}
	}

	$SMARTY->assign('documentcount', count($documentslist));
	$SMARTY->assign('taxxocount', count($result->{'Data'}));

	$SMARTY->assign('error', $error);
	$SMARTY->assignByRef('documentslist', $documentslist);
	$SMARTY->display('taxxoverify.html');
	exit;
}
$layout['pagetitle'] = trans('TAXXO');

$SMARTY->display('taxxoactions.html');
?>

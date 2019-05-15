<?php

$from = $_POST['from'];
$to = $_POST['to'];

if($from) {
	list($year, $month, $day) = explode('/',$from);
	$unixfrom = mktime(0,0,0,$month,$day,$year);
} else {
	$from = date('Y/m/d',time());
	$unixfrom = mktime(0,0,0); //today
}
if($to) {
	list($year, $month, $day) = explode('/',$to);
	$unixto = mktime(23,59,59,$month,$day,$year);
} else {
	$to = date('Y/m/d',time());
	$unixto = mktime(23,59,59); //today
}

$layout['pagetitle'] = trans('TAXXO Report for period $a - $b', $from, $to);


$taxxolist =$DB->GetAll('SELECT d.ctime, d.notdigitalize, d.docid, d.taxxoid, c.name,
						co.totalnet, co.totaltax, co.totalgross  
				FROM taxxo_documents d  
					LEFT JOIN taxxo_content co ON (co.tdocid=d.id)
					LEFT JOIN taxxo_contractor c ON (c.id=co.contractorid) 
				WHERE 1=1'
			.($unixfrom ? ' AND d.ctime>='.$unixfrom : '')
			.($unixto ? ' AND d.ctime<='.$unixto : ''));

if($taxxolist)foreach($taxxolist as $key => $val)
{
	$listdata['totalnet']+=$val['totalnet'];
	$listdata['totaltax']+=$val['totaltax'];
	$listdata['totalgross']+=$val['totalgross'];
}

$SMARTY->assign('taxxolist', $taxxolist);
$SMARTY->assign('listdata', $listdata);

$SMARTY->display('taxxoreport.html');

?>

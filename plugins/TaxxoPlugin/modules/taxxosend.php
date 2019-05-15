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
$layout['pagetitle'] = trans('TAXXO');

$SMARTY->display('taxxosendinvoices.html');
?>

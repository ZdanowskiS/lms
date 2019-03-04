<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=sellboardhostlist');
}
else
	$hostid = $_GET['id'];

if (!$BOARD->HostExists($hostid)) {
	$SESSION->redirect('?m=sellboardhostlist');
}

$hostdata = $BOARD->GetHost($hostid);

$layout['pagetitle'] = trans('Host Edit: $a',$hostdata['name']);

if(isset($_POST['hostedit']))
{
	$hostedit=$_POST['hostedit'];

	if($hostadd['name']==''){
		$error['name']=trans('Name is required!');
	}

	if($hostadd['url']==''){
		$error['url']=$hostdata['url'];
		$hostedit['url']=trans('URL is required! Setting previous value.');
	}

    if(!$error){
		$BOARD->HostUpdate($hostedit);
	}

	$hostdata = $hostedit;
}

$SMARTY->assign('error',$error);
$SMARTY->assign('hostdata',$hostdata);

$SMARTY->display('sellboardhostedit.html');
?>

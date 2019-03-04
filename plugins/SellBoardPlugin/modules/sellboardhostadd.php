<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$hostadd = isset($_POST['hostadd']) ? $_POST['hostadd'] : NULL;

if($hostadd)
{
	if($hostadd['name']==''){
		$error['name']=trans('Name is required!');
	}

	if($hostadd['url']==''){
		$error['url']=trans('URL is required!');
	}

	if(!$error)
	{
		$id = $BOARD->HostAdd($hostadd);

		if (!isset($categoryadd['reuse']))
			$SESSION->redirect('?m=sellboardhostinfo&id='.$id);
	}
}

$layout['pagetitle'] = trans('New Host');

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('error', $error);
$SMARTY->assign('hostadd', $hostadd);

$SMARTY->display('sellboardhostadd.html');
?>

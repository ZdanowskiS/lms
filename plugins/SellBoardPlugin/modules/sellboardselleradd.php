<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

$selleradd = isset($_POST['selleradd']) ? $_POST['selleradd'] : NULL;

if($selleradd)
{
	if($selleradd['name']==''){
		$error['name']=trans('Name is required!');
	}

	if(!$error)
	{
		$id = $BOARD->SellerAdd($selleradd);

		if (!isset($selleradd['reuse']))
			$SESSION->redirect('?m=sellboardsellerinfo&id='.$id);
	}
}

$layout['pagetitle'] = trans('New Seller');

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('error', $error);
$SMARTY->assign('selleradd', $selleradd);

$SMARTY->display('sellboardselleradd.html');

?>

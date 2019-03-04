<?php

$BOARD = SellBoardPlugin::getSellBoardInstance();

if (!preg_match('/^[0-9]+$/', $_GET['id'])) {
	$SESSION->redirect('?m=sellboardsellerlist');
}
else
	$sellerid = $_GET['id'];

if (!$BOARD->SellerExists($sellerid)) {
	$SESSION->redirect('?m=sellboardsellerlist');
}

$sellerdata = $BOARD->GetSeller($sellerid);

$layout['pagetitle'] = trans('Seller Edit: $a',$sellerdata['name']);

if(isset($_POST['selleredit']))
{
	$selleredit=$_POST['selleredit'];

	if($selleredit['name']==''){
		$error['name']=trans('Name is required!');
	}

    if(!$error){
		$BOARD->SellerUpdate($selleredit);
	}

	$sellerdata = $selleredit;
}

$SMARTY->assign('error',$error);
$SMARTY->assign('sellerdata',$sellerdata);

$SMARTY->display('sellboardselleredit.html');
?>

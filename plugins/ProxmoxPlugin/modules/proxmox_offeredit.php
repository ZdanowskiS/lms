<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$id=intval($_GET['id']);

if(!$PROXMOX->OfferExists($id)){
	$SESSION->redirect('?m=proxmox_offerlxclist');
}

$offerinfo=$PROXMOX->GetOffer($id);

if(isset($_POST['offeredit']))
{
	$offeredit = $_POST['offeredit'];

	if(!$error){
		$PROXMOX->OfferUpdate($offeredit);
		$SESSION->redirect('?m=proxmox_offerinfo&id='.$offeredit['id']);
	}
	else{
		$offerinfo=$offeredit;
		$SMARTY->assign('error', $error);
	}
}

$layout['pagetitle'] = trans('Proxmox').' - '.trans('Offert Edit: $a',$offerinfo['name']);

$SMARTY->assign('offerinfo', $offerinfo);
$SMARTY->assign('nodes', $PROXMOX->GetNodeNames());


$SMARTY->assign('bridgelist', $PROXMOX->GetBridges());
$SMARTY->assign('storagelist', $PROXMOX->GetStorage());
$SMARTY->assign('vztemplates', $PROXMOX->GetVZTemplates());
$SMARTY->assign('nodes', $PROXMOX->GetNodeNames());

$SMARTY->display('proxmox_offeredit.html');
?>

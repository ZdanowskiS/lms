<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$id = intval($_GET['id']);

if(!$PROXMOX->OfferExists($id))
	$SESSION->redirect('?m=proxmox_offerlist');

$offerinfo=$PROXMOX->GetOffer($id);

$layout['pagetitle'] = trans('Proxmox').' - '.trans('Offer Info: $a',$offerinfo['name']);

$SMARTY->assign('nodes', $PROXMOX->GetNodeNames());
$SMARTY->assign('offerinfo', $offerinfo);

$SMARTY->display('proxmox_offerinfo.html');
?>

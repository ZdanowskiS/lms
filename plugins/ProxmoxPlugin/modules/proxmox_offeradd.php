<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$layout['pagetitle'] = trans('Proxmox')." - ".trans("New Offer");

$offeradd = isset($_POST['offeradd']) ? $_POST['offeradd'] : NULL;

if($offeradd)
{
	if(!$offeradd['name'])
		$error['name']= trans('Name is required!');

    if(!isset($error))
	{
	    $id=$PROXMOX->OfferAdd($offeradd);

	    if (!isset($offeradd['reuse'])) {
		    $SESSION->redirect('?m=proxmox_offerinfo&id='.$id);
	    }
    }
}

$bridgelist= $PROXMOX->GetBridges();
$storagelist = $PROXMOX->GetStorage();
$vztemplates = $PROXMOX->GetVZTemplates();

$SMARTY->assign('nodeinfo', $nodeinfo);
$SMARTY->assign('bridgelist', $bridgelist);
$SMARTY->assign('storagelist', $storagelist);
$SMARTY->assign('vztemplates', $vztemplates);

$SMARTY->assign('error', $error);
$SMARTY->assign('nodes', $PROXMOX->GetNodeNames());

$SMARTY->assign('offeradd', $offeradd);

$SMARTY->display('proxmox_offeradd.html');
?>

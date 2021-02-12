<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$id= intval($_GET['id']);

if(!$PROXMOX->NodeExists($id))
	$SESSION->redirect('?m=proxmox_nodelist');

$nodeinfo=$PROXMOX->GetNode($id);

$layout['pagetitle'] = trans('Proxmox').' - '.trans('<!pr>Node Info: $a',$nodeinfo['name']);

$SMARTY->assign('nodeinfo', $nodeinfo);

$SMARTY->assign('bridgelist', $PROXMOX->GetNodeBridges($id));
$SMARTY->assign('storagelist', $PROXMOX->GetNodeStorages($id));
$SMARTY->assign('vztemplatelist', $PROXMOX->GetNodeVZTemplates($id));

$SMARTY->display('proxmox_nodeinfo.html');
?>

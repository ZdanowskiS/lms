<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$id=intval($_GET['id']);

$vmctinfo=$PROXMOX->GetVMCT($id);

$layout['pagetitle'] = trans('Proxmox').' - '.trans('VM/CT info: $a',$offerinfo['name']);

$SMARTY->assign('vmctinfo', $vmctinfo);

$SMARTY->display('proxmox_vmctinfo.html');
?>

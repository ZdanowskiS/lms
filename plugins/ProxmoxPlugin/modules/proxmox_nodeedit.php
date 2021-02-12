<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$id=intval($_GET['id']);

if(!$PROXMOX->NodeExists($id)){
	$SESSION->redirect('?m=proxmox_nodelist');
}

$nodeinfo=$PROXMOX->GetNode($id);

if(isset($_POST['nodeedit']))
{
	$nodeedit = $_POST['nodeedit'];

	if(!$nodeedit['name'])
		$error['name']= trans('Name is required!');

	if(!$nodeedit['ipaddr'])
		$error['ipaddr']= trans('IP address is required!');

	if(!$nodeedit['realm'])
		$error['realm']= trans('Realm is required!');

	if(!$nodeedit['login'])
		$error['login']= trans('Login is required!');

	if(!$nodeedit['password'])
		$error['password']= trans('Password is required!');

	if(!$error){
		$PROXMOX->NodeUpdate($nodeedit);
		$SESSION->redirect('?m=proxmox_nodeinfo&id='.$nodeedit['id']);
	}
	else{
		$nodeinfo=$nodeedit;
		$SMARTY->assign('error', $error);
	}
}

$layout['pagetitle'] = trans('Proxmox').' - '.trans('<!pr>Node Edit: $a',$nodeinfo['name']);

$SMARTY->assign('error', $error);
$SMARTY->assign('nodeinfo', $nodeinfo);

$SMARTY->display('proxmox_nodeedit.html');
?>

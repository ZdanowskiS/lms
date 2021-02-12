<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$id = intval($_GET['id']);

if($id)
{
	if(!$DB->getOne('SELECT id 
					    FROM proxmox_offerts
					        WHERE nodeid=?',array($id)))
	{
		$PROXMOX->DeleteNode($id);
	}
}

$SESSION->redirect('?m=proxmox_nodelist');
?>

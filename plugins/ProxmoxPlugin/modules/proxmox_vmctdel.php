<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$id = intval($_GET['id']);

if($id)
{
    $vmct=$PROXMOX->GetVMCT($id);
    $node=$PROXMOX->GetNode($vmct['nodeid']);
	$PROXMOX->connection->setlogin($node['name'],$node['ipaddr'], $node['login'], $node['realm'], $node['password']);

	if($PROXMOX->connection->login())
    {
        $data['vmid']=$vmct['vmid'];

        $PROXMOX->connection->deleteVMCT(($vmct['type']==PROXMOX_TYPE_CT ? 'lxc' : 'qemu'),$data['vmid']);

        $PROXMOX->DeleteVMCT($id);
    }
}

header('Location: ?'.$SESSION->get('backto'));
?>

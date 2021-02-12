<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

#print PROXMOX_TYPE_CT;
$id=$_GET['id'];
$status=$_GET['status'];

$vmct=$PROXMOX->GetVMCT($id);
$node=$PROXMOX->GetNode($vmct['nodeid']);
#print_r($node);
$PROXMOX->connection->setlogin($node['name'],$node['ipaddr'], $node['login'], $node['realm'], $node['password']);

if($PROXMOX->connection->login())
{
#$resources=$PROXMOX->connection->getResources();
#print_r($resources);
#print "<BR>";
$data=array('status' => $status);

$PROXMOX->connection->setVMCTstatus(($vmct['type']==PROXMOX_TYPE_CT ? 'lxc' : 'qemu'), $vmct['vmid'], $data);
}
/*
if($vmct['type']==3)
{
    $PROXMOX->connection->setCTstatus($data);
}
else
{
    $PROXMOX->connection->setVMstatus($data);
}
*/
if($vmct['customerid'])
    $SESSION->redirect('?m=customerinfo&id='.$vmct['customerid']);
else
header('Location: ?'.$SESSION->get('backto'));
?>

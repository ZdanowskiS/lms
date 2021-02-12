<?php

$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

$id=intval($_GET['id']);

if(!$PROXMOX->VMCTExists($id)){
	$SESSION->redirect('?m=proxmox_offerlxclist');
}


if(isset($_POST['vmctedit']))
{
    $vmctedit=$_POST['vmctedit'];
    $vmctedit['customerid']=$vmctedit['customer'];
    $PROXMOX->VMCTUpdate($vmctedit);

    $vmct=$PROXMOX->GetVMCT($vmctedit['id']);

    $node=$PROXMOX->GetNode($vmct['nodeid']);

	$PROXMOX->connection->setlogin($node['name'],$node['ipaddr'], $node['login'], $node['realm'], $node['password']);

	if($PROXMOX->connection->login())
    {
        $data=array('memory' => $vmctedit['maxmem'],
                'cores' => $vmctedit['cpus']);

        $PROXMOX->connection->setVMCToptions(($vmct['type']==PROXMOX_TYPE_CT ? 'lxc' : 'qemu'), $vmct['vmid'], $data);
    }
    $SESSION->redirect('?m=proxmox_vmctinfo&id='.$vmctedit['id']);
}
else
{
    $vmct=$PROXMOX->GetVMCT($id);

    $node=$PROXMOX->GetNode($vmct['nodeid']);
	$PROXMOX->connection->setlogin($node['name'],$node['ipaddr'], $node['login'], $node['realm'], $node['password']);

	if($PROXMOX->connection->login())
    {
        $apilist=$PROXMOX->connection->getVMCTinfo(($vmct['type']==PROXMOX_TYPE_CT ? 'lxc' : 'qemu'));

        if($apilist)foreach($apilist['data'] as $v)
        {
            if($v['vmid']==$vmct['vmid'])
            {
                $vmct['maxmem']=($v['maxmem']/1024/1024);
                $vmct['cpus']=$v['cpus'];
            }
        }
    }
}

$layout['pagetitle'] = trans('Proxmox').' - '.trans('VM/CT Edit: $a',$vmctdata['name']);

$LMS->InitXajax();
include(MODULES_DIR . DIRECTORY_SEPARATOR . 'nodexajax.inc.php');

$SMARTY->assign('xajax', $LMS->RunXajax());

$SMARTY->assign('networks', $LMS->GetNetworks(true));
$SMARTY->assign('nodes', $PROXMOX->GetNodeNames());
$SMARTY->assign('vmctdata', $vmct);

$SMARTY->display('proxmox_vmctedit.html');
?>

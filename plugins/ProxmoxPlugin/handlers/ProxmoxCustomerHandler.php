<?php

class ProxmoxCustomerHandler {
	public function customerInfoBeforeDisplay(array $hook_data) {
		global $LMS,$DB;

		$PROXMOX = ProxmoxPlugin::getProxmoxInstance();

		$SMARTY = $hook_data['smarty'];

		$vmctlist=$PROXMOX->GetCustomerVMCT($hook_data['customerinfo']['id']);

        if($vmctlist)foreach($vmctlist as $id => $vmct)
        {
            $node=$PROXMOX->GetNode($vmct['nodeid']);
            
            $PROXMOX->connection->setlogin($node['name'],$node['ipaddr'], $node['login'], $node['realm'], $node['password']);

            if($PROXMOX->connection->login())
            {

                $apilist= $PROXMOX->connection->getVMCTinfo(($vmct['type']==PROXMOX_TYPE_CT ? 'lxc' : 'qemu'));
                if($apilist)foreach($apilist['data'] as $v)
                {
                    if($vmct['vmid']==$v['vmid'])
                    {
                        $vmctlist[$id]['maxmem']=($v['maxmem']/1024/1024);
                        $vmctlist[$id]['status']=$v['status'];
                        $vmctlist[$id]['cpus']=$v['cpus'];
                    }
                } 
            }
        }
		$SMARTY->assign('vmctlist', $vmctlist);
		return $hook_data;
	}
}

?>

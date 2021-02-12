<?php

class ProxmoxNetworkHandler {
	public function networkrecord_after_get(array $hook_data) {
		global $LMS,$DB;

		extract($hook_data);

        if(!is_array($nodes))
            $nodes=array();

        $vmct=$DB->GetAllByKey('SELECT id, name, ipaddr, customerid as ownerid, 0 as netdev 
                            FROM proxmox_vmct WHERE ipaddr> ? AND ipaddr < ?', 'ipaddr',
                            array($network['addresslong'],ip_long($network['broadcast'])));

        $nodes= array_replace($nodes, $vmct);
        if(!is_array($nodes))
            $nodes=array();

        $proxmoxnodes=$DB->GetAllByKey('SELECT id, name, ipaddr, 0 as ownerid, 0 as netdev 
                            FROM proxmox_nodes WHERE ipaddr> ? AND ipaddr < ?', 'ipaddr',
                            array($network['addresslong'],ip_long($network['broadcast'])));

        $nodes= array_replace($nodes, $proxmoxnodes);

		return compact("id", "network", "nodes");
	}
}

?>

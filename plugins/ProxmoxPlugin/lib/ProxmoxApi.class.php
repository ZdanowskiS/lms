<?php

class ProxmoxApi {

	private $node;
	private $host;
	private $login;
	private $realm;
	private $passward;
	private $port;
	private $verify_ssl;
	private $ticket;

	public function __construct(){

	}

	public function setlogin($node, $host, $login, $realm, $password, $port = 8006, $verify_ssl = false)
	{
		$this->node=$node;
		$this->host=$host;
		$this->login=$login;
		$this->realm=$realm;
		$this->password=$password;
		$this->port=$port;
		$this->verify_ssl=$verify_ssl;
	}

	public function login() 
	{
		$login_postfields = array();
		$login_postfields['username'] = $this->login;
		$login_postfields['password'] = $this->password;
		$login_postfields['realm'] = $this->realm;

		$login_postfields_string = http_build_query($login_postfields);
		unset($login_postfields);

		$prox_ch = curl_init();
		curl_setopt($prox_ch, CURLOPT_URL, "https://{$this->host}:{$this->port}/api2/json/access/ticket");
		curl_setopt($prox_ch, CURLOPT_POST, true);
		curl_setopt($prox_ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($prox_ch, CURLOPT_POSTFIELDS, $login_postfields_string);
		curl_setopt($prox_ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);

		$login_ticket = curl_exec($prox_ch);


		$login_request_info = curl_getinfo($prox_ch);

		curl_close($prox_ch);
		unset($prox_ch);
		unset($login_postfields_string);

		if (!$login_ticket) {
			$this->login_ticket_timestamp = null;
			return false;
		}

		$result= json_decode($login_ticket, true);

		$this->ticket = $result['data'];

		return ($this->ticket ? TRUE : FALSE);
	}

	public function Execute($path, $method, $parameters = null)
	{
		$prox_ch = curl_init();
		curl_setopt($prox_ch, CURLOPT_URL, "https://{$this->host}:{$this->port}/api2/json{$path}");

		$http_headers = array();
		$http_headers[] = "CSRFPreventionToken: {$this->ticket['CSRFPreventionToken']}";

		curl_setopt($prox_ch, CURLOPT_HEADER, true);
		curl_setopt($prox_ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($prox_ch, CURLOPT_COOKIE, "PVEAuthCookie=".$this->ticket['ticket']);
		curl_setopt($prox_ch, CURLOPT_SSL_VERIFYPEER, false);

		switch ($method) {
			case "PUT":
				curl_setopt($prox_ch, CURLOPT_CUSTOMREQUEST, "PUT");

				$postfields_string = http_build_query($parameters);
				curl_setopt($prox_ch, CURLOPT_POSTFIELDS, $postfields_string);
				unset($postfields_string);

				curl_setopt($prox_ch, CURLOPT_HTTPHEADER, $http_headers);
				break;
			case "DELETE":
				curl_setopt($prox_ch, CURLOPT_CUSTOMREQUEST, "DELETE");

				curl_setopt($prox_ch, CURLOPT_HTTPHEADER, $http_headers);
				break;
			case "POST":
				curl_setopt($prox_ch, CURLOPT_POST, true);

				$postfields_string = http_build_query($parameters);
				curl_setopt($prox_ch, CURLOPT_POSTFIELDS, $postfields_string);
				unset($postfields_string);

				curl_setopt($prox_ch, CURLOPT_HTTPHEADER, $http_headers);
				break;
			default:
				break;
		}

		$action_response = curl_exec($prox_ch);

		curl_close($prox_ch);
		unset($prox_ch);

		$split_response = explode("\r\n\r\n", $action_response, 2);
		$header_response = $split_response[0];
		$body_response = $split_response[1];
		$response_array = json_decode($body_response, true);

		return $response_array;
	}

	public function get ($path) {
		return $this->Execute($path, "GET");
	}

	public function post($path, $parameters) {
		return $this->Execute($path, "POST", $parameters);
	}

	public function put($path, $parameters) {
		return $this->Execute($path, "PUT", $parameters);
	}

	public function delete($path, $parameters) {
		return $this->Execute($path, "DELETE", $parameters);
	}

	public function reload_node_list () {

		$node_list = $this->get("/nodes");
		if (count($node_list) > 0) {
			$nodes_array = array();
			foreach ($node_list as $node) {
				$nodes_array[] = $node['node'];
			}

			return true;
		} else {
			error_log(" Empty list of nodes returned in this cluster.");
			return false;
		}
	}

	public function startLxc($id)
	{
		$result = $this->post("/nodes/{$this->node}/lxc/$id/status/start");
		return $result;
	}

	public function stopLxc($id)
	{
		$result = $this->post("/nodes/{$this->node}/lxc/$id/status/stop");
		return $result;
	}

	public function deleteLxc($id)
	{
		$result = $this->delete("/nodes/{$this->node}/lxc/$id");
		return $result;
	}

	public function getLxcStatus($id)
	{
		$result = $this->get("/nodes/{$this->node}/lxc/$id/status/current");

		return $result;
	}

	public function getNode($node)
	{
		$result = $this->get("/nodes/$node");

		return $result;
	}

    public function getContent($node,$storage)
    {
		$result = $this->get("/nodes/$node/storage/$storage/content");

        return $result;
    }

	public function getNodeNetwork($node)
	{
		$result = $this->get("/nodes/$node/network");

		return $result;
	}

	public function getNodeStorage($node)
	{
		$result = $this->get("/nodes/$node/storage");

		return $result;
	}

	public function getTemplates()
	{
		$result = $this->get("/nodes/{$this->node}/storage/local/content");

		return $result;
	}

	public function getResources()
	{
		$result = $this->get("/cluster/resources");
		return $result;
	}

	public function addVM($data)
	{

		$param = array(
		   'vmid' => urlencode($data['vmid']),
		   'cores' => urlencode($data['cores']),
		   'sockets' => urlencode(1),
		   'name' => urlencode($data['hostname']),
		   'memory' => urlencode($data['memory']),
		   'onboot' => urlencode(1),
		   'storage' =>$data['storage']
		);

		$result = $this->post("/nodes/{$this->node}/qemu/", $param);

        $this->addVirtio($data['vmid'],$data);
        $this->addNet($data['vmid'],$data);

        return $result;
	}

    public function addVirtio($vmid,$param)
    {
        $param=array('virtio1' => $param['storage'].":".$param['size'].",format=qcow2");

		$result = $this->post("/nodes/{$this->node}/qemu/$vmid/config", $param);

        return $result;
    }

    public function addNet($vmid,$param)
    {
        $param=array('net1' => $param['nettype'].",bridge=".$param['bridge']);

		$result = $this->post("/nodes/{$this->node}/qemu/$vmid/config", $param);

        return $result;
    }

	public function addClone($param)
	{

		$paramb = array(
		    'newid' => urlencode($param['vmid']),
            'name' =>urlencode($param['hostname']),
		);
		
		$node_list = $this->post("/nodes/brick/qemu/".$param['clone']."/clone", $paramb);

		return $result;
	}

	public function addCT($param)
	{

		$param = array(
		   'vmid' => urlencode($param['vmid']),
		   'hostname' => $param['hostname'],
		   'cores' => urlencode($param['cores']),
		   'memory' => urlencode($param['memory']),
		   'onboot' => urlencode(1),
			'storage' => $param['storage'],
			'rootfs' => $param['rootfs'],
			'password' => $param['password'],
			'net0' => $param['net0'],
		   'ostemplate' =>$param['ostemplate'],
		);

		$result = $this->post("/nodes/{$this->node}/lxc",$param);

	}

    public function addLXCNet($vmid,$param)
    {
        $param=array('net1' => "e1000,bridge=vmbr1");

		$result = $this->post("/nodes/{$this->node}/lxc/$vmid/config", $param);

        return $result;
    }

    public function getVMCTinfo($type)
    {
		$result = $this->get("/nodes/{$this->node}/$type");
        return $result;
    }

    public function setVMCTstatus($type, $vmid, $data)
    {
		$result = $this->post("/nodes/{$this->node}/$type/".$vmid."/status/".$data['status'],array());

        return $result;
    }

    public function setVMCToptions($type, $vmid, $data)
    {

		$result = $this->put("/nodes/{$this->node}/$type/$vmid/config", $data);

        return $result;
    }

    public function deleteVMCT($type,$vmid)
    {
        print "/nodes/{$this->node}/$type/".$vmid;
        $result = $this->delete("/nodes/{$this->node}/$type/".$vmid,array());

        return $result;
    }

}

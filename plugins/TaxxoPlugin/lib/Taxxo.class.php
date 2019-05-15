<?php

abstract class Taxxo {

	protected $token;
	protected $url;
	protected $api_key;

	public function __construct($url,$api_key) { 
		$this->url=$url;
		$this->api_key=$api_key;
	}

	public function ApiAuthenticate()
	{
		$data = json_encode($this->api_key);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($curl, CURLOPT_URL, $this->url.'authenticate');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
    		'Accept: application/json',
		));
		curl_setopt($curl, CURLOPT_POST, 1);

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($curl);
		$json = json_decode($result);

		$this->token=$json->{'Data'};
		return;
	}

	public function ApiLogout()
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($curl, CURLOPT_URL, $this->url.'logout');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
    		'Accept: application/json',
			'authorization: '.$this->token
		));
		curl_setopt($curl, CURLOPT_POST, 1);

		$result = curl_exec($curl);

		return $result;
	}

	public function ApiPOST($action,$data)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt($curl, CURLOPT_URL, $this->url.$action);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
    		'Accept: application/json',
			'authorization: '.$this->token
		));
		curl_setopt($curl, CURLOPT_POST, 1);

		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($curl);

		return json_decode($result);
	}

	public function ApiGET($action)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url.$action);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
    		'Accept: application/json',
			'authorization: '.$this->token
		));
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);

		$result = curl_exec($curl);

		return json_decode($result);
	}

	public function ApiDELETE($action)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->url.$action);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
    		'Accept: application/json',
			'authorization: '.$this->token
		));
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);

		$result = curl_exec($curl);

		return json_decode($result);
	}
}

?>

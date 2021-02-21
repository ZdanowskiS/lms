<?php

class LibreNMSApi {

	private $host;
	private $token;

	public function __construct(){
		$this->host=ConfigHelper::getConfig('librenms.host', '');
        $this->token=ConfigHelper::getConfig('librenms.token', '');
	}

	public function Execute($path, $method, $parameters = null)
	{
		$prox_ch = curl_init();
		curl_setopt($prox_ch, CURLOPT_URL, "http://{$this->host}/api/v0/{$path}");

		$http_headers = array();
		$http_headers[] = "X-Auth-Token: {$this->token}";

		curl_setopt($prox_ch, CURLOPT_HEADER, true);
		curl_setopt($prox_ch, CURLOPT_RETURNTRANSFER, true);

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
                curl_setopt($prox_ch, CURLOPT_HTTPHEADER, $http_headers);
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
}

?>

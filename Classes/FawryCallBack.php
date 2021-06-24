<?php

class FawryCallBack
{
	protected $merchantRefNum = '';
	
	function __construct($merchantId)
	{
		if($merchantId) {
			$this->merchantRefNum = $merchantId;
		}
	}
	
	/**
	 * Sample Request:
	 * 
	 * Http Method : GET
	 */ 
	public function fawryCallBack()
	{
		$merchantCode = '';
		$secureKey = '';
		$url = 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/payments/status';
		
		$data = [
			"merchantCode" => $merchantCode,
			"merchantRefNumber" => $this->merchantRefNum,
			"signature" => hash("sha256", $merchantCode.$this->merchantRefNum.$secureKey)
		];
		
		$response = $this->fawryCallBackAPI($url, $data);
		
		return $response; 
	}

	// create fawry callback url GET request
	public function fawryCallBackAPI($url, $data)
	{
		$callBackUrl = '';

		foreach($data as $key => $value)
		{
		$callBackUrl .=  $key . "=" . $value . "&";
		}
		$callBackUrl =  $url . '?' . substr($callBackUrl, 0, strlen($callBackUrl)-1);

		$ch = curl_init();

		curl_setopt_array($ch, [
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_URL => $callBackUrl,
		CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"content-type: application/json",
		)
		]);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);
		
		curl_close($ch);

		return $response;
	
	}

	public function fawryCallBackPost()
	{
		//
	}
}
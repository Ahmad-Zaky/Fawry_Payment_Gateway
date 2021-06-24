<?php

class Fawry
{
	protected $payMethod = "PAYATFAWRY";
	protected $currencyCode = "EGP";

	// Fawry pay API Gateway
	public function request($customer, $order)
	{
		$secureKey = "";
		$merchantCode = "";
		$merchantRefNum = $order['merchantRefNum'];  
		$customerProfileId = $customer['firstname'] .' '. $customer['lastname'];
		// $customerProfileId = (int)$customer['contactid'];
		$customerMobile = $customer['mobile'];
		$customerEmail = $customer['email'];
		$total_price = $order['amount'];
		$description = $order['description'] ? $order['description'] : 'fawry order';
		$paymentMethod = $this->payMethod;
		$currencyCode = $this->currencyCode;

		$data = [
			"merchantCode"=> $merchantCode,
			"merchantRefNum"=> $merchantRefNum,
			"customerProfileId"=> $customerProfileId,
			"customerMobile"=> $customerMobile,
			"customerEmail"=>  $customerEmail,
			"paymentMethod"=> $paymentMethod,
			"amount"=> $total_price,
			"currencyCode"=> $currencyCode,
			"description"=> $description,
			"paymentExpiry"=> $order['expiryDate'],
			"chargeItems"=> $order['items'],
			"signature"=> hash('sha256', $merchantCode.$merchantRefNum.$customerProfileId.$paymentMethod.$total_price.$secureKey)
		];
		$url = "https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/payments/charge";
		
		$response = $this->callFawryApi($url, $data);
		$response = json_decode($response, true);
		
		if (isset($response['statusCode']) && $response['statusCode'] == 200) {
			// success
			return [
				'status'            => true,
				'referenceNumber'   => $response['referenceNumber'],
				'merchantRefNumber' => $response['merchantRefNumber'],
			];
		}else{
			return [
				'status' => false,
				'error'  => (isset($response['statusDescription'])) ? $response['statusDescription'] : $response['description'],
				'statusCode'  => (isset($response['statusCode'])) ? $response['statusCode'] : 500,
			];
		}
	}

    /**
     * Sample Request:
     * 
     * Http Method : GET
     * 
     */ 
    public function fawryCallBack(Request $request)
    {
        $referenceNumber = '';
        $merchantCode = '';
        $merchantRefNum = '';
        $secureKey = '';
        $url = 'https://atfawry.fawrystaging.com/ECommerceWeb/Fawry/payments/status';

        $data = [
          "merchantCode" => $merchantCode,
          "merchantRefNumber" => $merchantRefNum,
          "signature" => hash("sha256", $merchantCode.$merchantRefNum.$secureKey)
        ];

        $response = $this->fawryCallBackAPI($url, $data);
        echo $response;
    }

    // may be not necessary
    public function fawryCallbackResponse(Request $request)
    {
      // dd($request); // Laravel Code
    }

    // create fawry POST request
    public function callFawryApi($url, $data)
    {
      
		//Initiate cURL.
		$ch = curl_init($url);
		
		//The JSON data.
		$jsonData = $data;
		
		//Encode the array into JSON.
		$jsonDataEncoded = json_encode($jsonData);
		
		//Tell cURL that we want to send a POST request.
		curl_setopt($ch, CURLOPT_POST, true);
		
		//Attach our encoded JSON string to the POST fields.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

		//return the output from curl_exec() as a string value
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//Set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		//Execute the request
		$result = curl_exec($ch);

		// in case there is an error we can show it here using this code
		// var_dump('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		// die();

		// Close curl
		curl_close($ch);
		
		return $result;
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

		return redirect($callBackUrl);
		
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
		
		// in case there is an error we can show it here using this code
		// dd('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		//   dd(curl_getinfo($ch));
		
		curl_close($ch);
		return $response;
    }

    public function callApi($url,$data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            // CURLOPT_URL => "https://atfawry.fawrystaging.com//ECommerceWeb/Fawry/payments/charge",
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                // Set here requred headers
                "accept: /",
                "accept-language: en-US,en;q=0.8",
                "content-type: application/json",
            ),
        ));

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = json_decode(curl_exec($curl));

        curl_close($curl);

        return $response;
    }
}
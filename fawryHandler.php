<?php

include_once('Classes/Fawry.php');

function fawryHandler() {

    // get Customer Info
    $customerInfo = getCustomerInfo();
    if (validateCustomerInfo($customerInfo))
        return false;

    // get fawry (Order) Information
    $fawryInfo = getFawryInfo();
    if (validateFawryInfo($fawryInfo))
        return false;

    processFawry($customerInfo, $fawryInfo);
}

function processFawry($customerInfo, $fawryInfo)
{
    // Get Fawry Information
    $merchantRefNum = $fawryInfo['merchantRefNum'];
    $amount = $fawryInfo['amount'];
    $expDate = $fawryInfo['expDate'];
    $description = empty($fawryInfo['description']) ? '' : $fawryInfo['description'];

    // Set Fawry (Order)
    $fawryOrder = [
      "merchantRefNum" =>  $merchantRefNum, // merchantRefNum (fawry id)
      "amount" => validateAmountFormat($amount),
      "expiryDate" => validateExpDate($expDate),
      "description" => $description,
      "items" => [
        [ // Static Data
          "itemId"=> "1",
          "description"=> "Subscription",
          "price"=> $amount,
          "quantity"=> 1
        ]
      ]
    ];

    $response = sendFawry($customerInfo, $fawryOrder);

    print_r($response);
}

function sendFawry($customerInfo, $fawryOrder)
{
    // Send Fawry Request
    $fawry = new Fawry();
    $response = $fawry->request($customerInfo, $fawryOrder);
    
    // Return Resposne
    if(!isset($response['status']))
        return [
            'success' => false,
            'message' => 'Fawry Request Failed!',
            'response' => $response,
        ];
    
    return  [
        'success' => true,
        'message' => 'Fawry Request Succeed!',
        'response' => $response,
        'referenceNumber' => (string)$response['referenceNumber']
    ];
}

function getCustomerInfo()
{
    $firstname = '';
    $lastname = '';
    $mobile = '';
    $email = '';

    $customer = [
        "firstname" => $firstname,
        "lastname" => $lastname,
        "mobile" => $mobile,
        "email" => $email
    ];
    return $customer;
}

function getFawryInfo()
{
    $merchantRefNum = '';
    $amount = '';

    $fawryInfo = [
        "merchantRefNum" => $merchantRefNum,
        "amount" => $amount
    ];
    return $fawryInfo;
}

// validate amount format to be like this '20.00 ,or '2000.10'
function validateAmountFormat($amount)
{
    if($amount) {
        $dotPos = strrpos($amount, '.');
        if($dotPos !== false)
        {
        if($dotPos+3 === strlen($amount))
            return $amount;
        else {
            return substr($amount, 0, $dotPos+3);
        }
        } else {
        return $amount . '.00';
        }
    }
    return false;
}

// validate timestamp if ok we multiply timestamp by 1000 to convert it to milliseconds
function validateExpDate($expDate)
{
    return $expDate ? (strtotime($expDate) < time() ? '' : strtotime($expDate)*1000) : '';
}

// Validate Customer Info
function validateCustomerInfo($customerInfo)
{ 
    return !empty($customerInfo) ? $customerInfo : false;
    
    // TODO: validate Customer Info data
}

// Validate Fawry Info
function validateFawryInfo($fawryInfo)
{
    return !empty($fawryInfo) ? $fawryInfo : false;
    
    // TODO: validate Fawry Info data
}

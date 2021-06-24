<?php 

include_once('Classes/FawryCallBackGen.php');

$fawryCallBk = new FawryCallBack();
$response = $fawryCallBk->fawryCallBack();

if(isset($response)) {
    $response = json_decode($response, true);
    if($response['PaymentStatusResponse']) {
        
        print_r($response); echo '<br>';

        $refNumber = (string)$response['referenceNumber'];
        $paymentStatus = $response['paymentStatus'];

        echo $refNumber . '<br>';
        echo $paymentStatus . '<br>';
    }
}

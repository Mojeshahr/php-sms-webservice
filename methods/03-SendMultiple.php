<?php
                    
function SendMultiple($Destination , $UserTraceId , $Text  ){
    $ApiKey = "e883424d-d70f-4e58-8ee3-4e21ea390ff1";
    $Sender = 30007546464646;
    class Recipients{
        public $Sender;
        public $Text;
        public $Destination;
        public $UserTraceId;
    }
    $Recipient = new Recipients();
    $Recipient->Sender = $Sender;
    $Recipient->Text = $Text;
    $Recipient->Destination = $Destination;
    $Recipient->UserTraceId = $UserTraceId;
    $Recipients = array($Recipient);

$data = array(
    'ApiKey'=>$ApiKey,
    'Recipients'=>$Recipients
);
$data_json = json_encode($data);

$url = 'http://api.sms-webservice.com/api/V3/SendMultiple';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response  = curl_exec($ch);
curl_close($ch);
return $response;
}

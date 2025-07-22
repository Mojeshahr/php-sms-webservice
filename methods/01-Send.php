<?php

    function Send($recipients , $text){
        $ApiKey = "e883424d-d70f-4e58-8ee3-4e21ea390ff1";
        $sender = "30007546464646";
    

        $params = array(
            'ApiKey' => $ApiKey,
            'Text' => urlencode($text),
            'Sender' => $sender,
            'Recipients' => $recipients,
        );


        $url = 'http://api.sms-webservice.com/api/V3/Send?' . http_build_query($params);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    
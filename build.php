<?


    //$handle = fopen("http://integration.cdek.ru/v1/location/regions/json?countryName=RU", "rb");
   // $contents = stream_get_contents($handle);
    //fclose($handle);
    //$data=json_decode($contents,true);

    //p/rint_r($data);

    $url = "http://integration.cdek.ru/v1/location/regions/json?countryCode=RU";

    $headers[] = "Content-Type: application/json";
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
           //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
          //  curl_setopt($ch, CURLOPT_POST, 0);
        $output = curl_exec($ch);
        curl_close ($ch);
        $data = json_decode($output,true);
        echo (count($data));

        


   
?>
<?
    include_once 'lib/boxberry.php';
    include_once 'lib/cdek.php';
    include_once 'lib/postal.php';
    include_once 'lib/dalli-service.php';

    $get_data = $_GET;
    $weight = $get_data['data']['weight'];
    $endCity = $get_data['data']['endCity'];
    $orderSum = $get_data['data']["orderSum"];
    $length = $get_data['data']["length"];
    $width = $get_data['data']["width"];
    $height = $get_data['data']["height"];
    $regionCode = $get_data['data']["region"]['regionCode'];
    $regionName = $get_data['data']["region"]['name'];
    $price = $get_data['data']["cost"];
    $orderSum = $get_data['data']["orderSum"];
    $response;

/**    
    $dalli = new DalliService ("");
    $dalli->SetParam($weight,$regionName,$endCity,$width,$height,$length, $price, $orderSum);
    $response['pvz'][]=$dalli->GetPrice($dalli->typePVZ);
    $response['kur'][]=$dalli->GetPrice($dalli->typeKUR);

    $boxberry = new BoxberryService("https://api.boxberry.ru/json.php", "");
    $boxberry->setParam($weight,$endCity,$width,$height,$length, $price, $orderSum);
    $response['pvz'][]=$boxberry->GetDeliveryCosts(false);
    $response['kur'][]=$boxberry->GetDeliveryCosts(true);

    $postal = new Postal ("", "");
    $postal->SetParam ($weight, $endCity, $width, $height, $length, $price, $orderSum);
    $response['post'][] = $postal->GetPrice("POSTAL_PARCEL");
    $response['post'][] = $postal->GetPrice("EMS");
    


    $cdek = new cdekService("", "");
    $cdek->SetParam($weight, $regionCode, $endCity, $width, $height, $length, $price, $orderSum);
    $result = $cdek->GetPrice();
    if ($result!=false) {
        $r = json_encode(array_merge(json_decode($result, true),$response));
        echo $r;
    } else echo json_encode($response);
    **/
?>

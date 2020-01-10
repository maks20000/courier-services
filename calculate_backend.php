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

   // if ($product!="") putInFile($weight, $width, $height, $length, $product, $price);
    
    $dalli = new DalliService ("ed9fc57846ed6ef8331be2710ee930de");
    $dalli->SetParam($weight,$regionName,$endCity,$width,$height,$length, $price, $orderSum);
    $response['pvz'][]=$dalli->GetPrice($dalli->typePVZ);
    $response['kur'][]=$dalli->GetPrice($dalli->typeKUR);

    $boxberry = new BoxberryService("https://api.boxberry.ru/json.php", "c17dc20337e4ba9d62cbed56f6a84736");
    $boxberry->setParam($weight,$endCity,$width,$height,$length, $price, $orderSum);
    $response['pvz'][]=$boxberry->GetDeliveryCosts(false);
    $response['kur'][]=$boxberry->GetDeliveryCosts(true);

    $postal = new Postal ("0HO0Zkq2mDqlAAm7GjXfU1Qp2X4tMmt6", "ZC1rYXJwaWtpQHlhbmRleC5jb206UG9jaHRAMjAxOQ==");
    $postal->SetParam ($weight, $endCity, $width, $height, $length, $price, $orderSum);
    $response['post'][] = $postal->GetPrice("POSTAL_PARCEL");
    $response['post'][] = $postal->GetPrice("EMS");
    


    $cdek = new cdekService("2d3hGnJWn949P8gncPt1vVVvTApZKxDm", "aGpYuJxNRT9OhDcLFAxVJCi9CBug765a");
    $cdek->SetParam($weight, $regionCode, $endCity, $width, $height, $length, $price, $orderSum);
    $result = $cdek->GetPrice();
    if ($result!=false) {
        $r = json_encode(array_merge(json_decode($result, true),$response));
        echo $r;
    } else echo json_encode($response);
?>
<?

class cdekService {
    private $login;
    private $secure;
    private $weight=1;
    private $region;
    private $city;
    private $width=1;
    private $height=1;
    private $length=1;
    private $price=0;
    private $orderSum=0;
    public function __construct($login, $secure){  
        $this->login = $login;
        $this->secure = $secure;
    }

    private function request ($url, $params, $headers) {
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        curl_close ($ch);
        return $output;
    }

    private function requestGet ($url) {
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close ($ch);
        return $output;
    }

    private function CityCode ($region) {
        for ($i=0; true; $i++) {
            $url = "http://integration.cdek.ru/v1/location/cities/json?regionCode=".$region."&page=".$i;
            $data = $this->requestGet($url);
            $data = json_decode($data,true);
            if (count($data)==0) break;
            foreach ($data as $item) {
                if ($item['cityName']==$this->city) {
                    return $item['cityCode'];
                }
            }
        }
        return false;
    }

    public function GetRegions () {
        $array = file_get_contents('regions.txt');
        $data = json_decode($array, true);
        return $data;
    }

    public function GetPrice () {
        $cityCode = $this->CityCode($this->region);
        if ($cityCode!=false) {
            $params = [
                "version"=>"1.0",
                "authLogin" =>$this->login,
                "secure" =>$this->secure,
                "dateExecute"=>date("Y-m-d"),
                "senderCityId"=>"44",
                "receiverCityId"=>$cityCode,
                "currency"=>"RUB",
                "tariffList"=>
                    [
                        [
                            "id"=>137
                        ], 
                        [
                            "id"=>233
                        ],
                        [
                            "id"=>136
                        ],
                        [
                            "id"=>234
                        ],
                    ],
                "goods"=>
                    [
                        [
                            "weight"=>$this->weight,
                            "length"=>$this->length,
                            "width"=>$this->width,
                            "height"=>$this->height
                        ]
                    ],
            ]; 
            $url = "http://api.cdek.ru/calculator/calculate_tarifflist.php";
            $headers[] = "Content-Type: application/json";
            $data = $this->request ($url, $params, $headers);
            return $data;
        }
        return null;
    }

    public function SetParam ($weight=1, $region=57, $city="Самара", $width=1, $height=1, $length=1, $price=0, $orderSum=0) {
        $this->weight=$weight;
        $this->region=$region;
        $this->city=$city;
        $this->width=$width;
        $this->height=$height;
        $this->length=$length;
        $this->price=$price;
        $this->orderSum=$orderSum;
    }
/** 
    public function ResultArray($array) {
        $r;
        $return;
        $result=$array['result'];
       // print_r($result);
        foreach ($result as $item) {
            $error="";
            $errormsg="";
            $name="СДЭК";
            $tariff;
            $typedelivery;
            $price = $item['result']['price'];
            $delivery = $item['result']['deliveryPeriodMin']."-".$item['result']['deliveryPeriodMax']." дн.";
            if ($item['tariffId']==136) {
                $tariff="Посылка (склад-склад)";
                $typedelivery=0;
            }
            if ($item['tariffId']==137) {
                $tariff="Посылка (склад-дверь)";
                $typedelivery=1;
            }
            if ($item['tariffId']==233) {
                $tariff="Экономичная посылка (склад-дверь)";
                $typedelivery=1;
            }
            if ($item['tariffId']==234) {
                $tariff="Экономичная посылка (склад-склад)";
                $typedelivery=0;
            }

            if ($item['result']['errors']['code']!=null) {
                $error=$item['result']['errors']['code'];
                $errormsg=$item['result']['errors']['text'];
            }
            
            $r[] = [
                'name'=>$name,
                'typedelivery'=>$typedelivery,
                'tariff'=>$tariff,
                'price'=>$price,
                'delivery'=>$delivery
            ];
        }
        foreach ($r as $item) {
            if ($item['typedelivery']==0) {
                $return['pvz'][]=$item;
            }
            else $return['kur'][]=$item;
        }
        return $return;
    }
    **/
}


?>
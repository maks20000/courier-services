<?

class BoxberryService {
    private $url;
    private $token;
    private $weight=0;
    private $target=0;
    private $width=0;
    private $height=0;
    private $depth=0;
    private $boxberry=0;
    private $price=0;
    private $orderSum=0;
    public function __construct($url, $token){  
        $this->url = $url;
        $this->token = $token;
    }

    private function request ($url) {

        $handle = fopen($url, "rb");
        $contents = stream_get_contents($handle);
        fclose($handle);
        $data=json_decode($contents,true);
        if(count($data)<=0 or $data[0]['err'])
        {
            return false;
        }
        else {
            return $data;
        }
    }

    public function ListPointsShort ($city_name) {
        $code = $this->GetCityCode ($city_name);
        if ($code==false) return false;
        $url = $this->url."?"."token=".$this->token."&method=ListPointsShort&CityCode=".$code;
        $data = $this->request($url);
        if (count ($data)>0) {
            $last = count ($data)-1;
            return $data[$last]['Code'];
        }
        return false;
    }

    public function GetCityCode ($city_name) {
        $url = $this->url."?"."token=".$this->token."&method=ListCitiesFull";
        $data = $this->request($url);
        foreach ($data as $city) {
            if ($city["Name"]==$city_name) {
                return $city['Code'];
            }
        }
        return false;
    }

    private function CheckCourier ($city_name) {
        $url = $this->url."?"."token=".$this->token."&method=CourierListCities";
        $data = $this->request($url);
        foreach ($data as $city) {
            if ($city["City"]==$city_name) {
                return true;
            }
        }
        return false;
    }

    private function GetZip ($city_name) {
        if ($this->CheckCourier($city_name)) {
            $url = $this->url."?"."token=".$this->token."&method=ListZips";
            $data = $this->request($url);
            foreach ($data as $city) {
                if ($city["City"]==$city_name) {
                    return $city["Zip"];
                }
            }
        }
        return false;
    }

    public function GetDeliveryCosts ($courier) {
        $main = $this->ListPointsShort("Москва");
        $targetCode = $this->ListPointsShort($this->target);
        if ($targetCode!=false) {
            $url = $this->url."?"."token=".$this->token."&method=DeliveryCosts&"."weight=".$this->weight."&target=".$targetCode."&targetstart=".$main."&height=".$this->height
            ."&width=".$this->width."&depth=".$this->depth."&sucrh=1";
            //."&ordersum=".$this->orderSum
          //  ."&paysum=".$this->price;
            if ($courier && $this->CheckCourier($this->target)) {
                $zip = $this->GetZip($this->target);
                $url.="&zip=".$zip;
            }
            $data = $this->request($url);
        }
        else $data=false;
        $data = $this->ResultArray($data, $courier);
        return $data;
    }

    public function SetParam ($weight, $target, $width=0, $height=0, $depth=0, $price=0, $orderSum=0) {
        $this->weight=$weight*1000;
        $this->target=$target;
        $this->width=$width;
        $this->height=$height;
        $this->depth=$depth;
        $this->price = $price;
        $this->orderSum=$orderSum;
    }

    private function ResultArray($array, $typedelivery) {
        $error = "";
        $delivery = $array['delivery_period']." дн.";
        $price = $array['price'];
        if ($array==false) {
            $error = 1;
        }
        $type=1;
        $tariff="Курьер";
        $r;
        
        
        if (!$typedelivery) {
            $type=0;
            $tariff="ПВЗ";
        }
        if ($price=="") $delivery="-";
        $r = [
            "name"=>"Boxberry",
            'error'=>$error,
            'errormsg'=>"Не удалось получить стоимость",
            'typedelivery'=>$type,
            'tariff'=>$tariff,
            'price'=>$array['price'],
            'delivery'=>$delivery
        ];

        return $r;
    }

}

?>
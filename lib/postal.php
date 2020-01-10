<?

    class Postal {

        private $token;
        private $authCode;
        private $weight=1;
        private $city;
        private $width=1;
        private $height=1;
        private $length=1;
        private $price=0;
        private $orderSum=0;
        private $url ="https://otpravka-api.pochta.ru";
        public function __construct($token, $authCode){  
            $this->token = $token;
            $this->authCode = $authCode;
        }
        private function request ($url, $params) {
            $ch = curl_init();
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHeaders());
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $output = curl_exec($ch);
            curl_close ($ch);
            return $output;
        }

        private function GetHeaders () {
             $headers=[
                "Content-Type:application/json",
                "Accept:application/json;charset=UTF-8",
                "Authorization:AccessToken " . $this->token,
                "X-User-Authorization:Basic " . $this->authCode,
            ];
            return $headers;
        }

        private function requestGet ($url) {
            $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $this->GetHeaders());
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $output = curl_exec($ch);
            curl_close ($ch);
            return $output;
        }

        public function GetShippingPoint () {
            $url=$this->url."/1.0/user-shipping-points";
            $output = $this->requestGet($url);
            $data = json_decode($output,true);
            foreach ($data as $item) {
                if ($item['enabled']==true)  return $item['operator-postcode'];
            }
            return false;
        }

        public function GetZip() {
            $url="https://otpravka-api.pochta.ru/postoffice/1.0/settlement.offices.codes?settlement=".urlencode($this->city);
            $output = $this->requestGet($url);
            $data = json_decode($output);
            if (count ($data)>0) {
                $c = 1;
                return $data[$c];
            }
            return false;
        }

        public function GetPrice ($type) {

            $from = $this->GetShippingPoint ();
            $to = $this->GetZip();
            $category="ORDINARY";
            if ($this->orderSum>0) $category="WITH_DECLARED_VALUE";
            if ($from==false) $from="130202";
            $params = [
                "index-from"=> "$from",
                "index-to"=> "$to",
                "declared-value"=> $this->orderSum,
                "mail-category"=> $category,
                "courier"=> true,
                "mail-type"=> "$type",
                "mass"=> $this->weight,
                "dimension"=> [
                    "height"=> $this->height,
                    "length"=> $this->length,
                    "width"=> $this->width
                ]
            ]; 
            $url = $this->url."/1.0/tariff";
            $output = $this->request ($url, $params, $headers);
            $data = json_decode($output, true);
            if ($data['code']) return $this->Error($data,$type);
            return $this->ResultArray($data, $type);
        }

        private function Error($data,$type) {
            $tariff = "Обычная посылка";
            $name = "Почта";
            if (strcmp($type,"EMS")==0) {
                $tariff = "EMS";
                $name = "Почта EMS";
            }
            $r = [
                "name"=>$name,
                'error'=>$data['code'],
                'errormsg'=>$data['desc'],
                'typedelivery'=>$type,
                'tariff'=>$tariff." - ".$data['desc'],
                'price'=>"-",
                'delivery'=>""
            ];
            return $r;
        }

        private function ResultArray($data, $type){
            
            $maxDay=$data['delivery-time']['max-days'];
            $minDay=$data['delivery-time']['min-days'];
            $name = "Почта";
            if ($minDay!="") $minDay.="-";
            $delivery = $minDay.$maxDay." дн.";
            $price = ($data['total-rate']+$data['total-vat'])/100;
            $tariff = "Обычная посылка";
            if (strcmp($type,"EMS")==0) {
                $tariff = "EMS";
                $name = "Почта EMS";
            }
            $type=1;
            $r = [
                "name"=>$name,
                'error'=>0,
                'errormsg'=>"",
                'typedelivery'=>$type,
                'tariff'=>$tariff,
                'price'=>$price,
                'delivery'=>$delivery
            ];

            return $r;
        }

            public function SetParam ($weight=1, $city="Самара", $width=1, $height=1, $length=1, $price=0, $orderSum=0) {
                $this->weight=$weight*1000;
                $this->city=$city;
                $this->width=$width;
                $this->height=$height;
                $this->length=$length;
                $this->price=$price*100;
                $this->orderSum=$orderSum*100;
            }

    }
?>
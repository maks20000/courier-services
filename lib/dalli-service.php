<?php


class DalliService {
        private $token;
        private $weight=1;
        private $region;
        private $city;
        private $width=1;
        private $height=1;
        private $length=1;
        private $price=0;
        private $orderSum=0;
        public $typeKUR=1;
        public $typePVZ=0;
        public function __construct($token){  
            $this->token = $token;
        }

        private function request ($url, $params, $headers) {
            $ch = curl_init();
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $output = curl_exec($ch);
            $oXML = new SimpleXMLElement($output);
            curl_close ($ch);
            return $oXML;
        }

        public function GetPrice ($typedelivery) {
                $type="KUR";
                if ($typedelivery==$this->PVZ)
                    $type="PVZ";
                $params ='
                <?xml version="1.0" encoding="UTF-8"?>
                <deliverycost>
                  <auth token="'.$this->token.'"></auth>
                  <partner>DS</partner>
                  <oblname>'.$this->region.'</oblname>
                  <townto>'.$this->city.'</townto>
                  <weight>'.$this->weight.'</weight>
                  <length>'.$this->length.'</length>
                  <width>'.$this->width.'</width>
                  <height>'.$this->height.'</height>
                  <typedelivery>'.$type.'</typedelivery>
                </deliverycost>';
                $url = "https://api.dalli-service.com/v1/";
                $headers=[
                    "Content-Type: application/xml",
                ];
                $data = $this->request ($url, $params, $headers);
                
                //if ($data['error']>0) return false;
                
                $data = $this->ResultArray($data, $typedelivery);
                return $data;
        }

        public function SetParam ($weight=1,$region, $city="Самара", $width=1, $height=1, $length=1, $price=0, $orderSum=0) {
            $this->weight=$weight;
            $this->region=$region;
            $this->city=$city;
            $this->width=$width;
            $this->height=$height;
            $this->length=$length;
            $this->price=$price;
            $this->orderSum=$orderSum;
        }


        private function ResultArray($array, $typedelivery) {
            $type=1;
            $tariff="Курьер";
            $r;
            $delivery=(string)($array['delivery_period'])." дн.";
            
            //print_r($array['price']);
            if ($typedelivery==$this->typePVZ) {
                $type=0;
                $tariff="ПВЗ";
            }
            if ((integer)$array['error']>0) {
                $delivery="-";
            }
            $r = [
                "name"=>"Dalli-service",
                'error'=>(integer)$array['error'],
                'errormsg'=>"Услуга не оказывается",
                'typedelivery'=>$type,
                'tariff'=>$tariff,
                'price'=>(double)$array['price'],
                'delivery'=>$delivery
            ];

            return $r;
        }


}

?>
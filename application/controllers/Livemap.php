<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();


class Livemap extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Devices_model");
        $this->load->model("Gateways_model");
        $this->load->model("Buildings_model");
    }

    public function redis()
    {
        if ($this->config->item('redis_status') == TRUE) {
            $client = new Predis\Client([
                'scheme' => $this->config->item('redis_scheme'),
                'host'   => $this->config->item('redis_host'),
                'port'   => $this->config->item('redis_port'),
                'password' => $this->config->item('redis_auth')
            ]);
            return $client;
        }
    }

    public function index()
    {
        $client = $this->redis();

        $dosya = '151_0_p.xml';
        //$tum = __DIR__.'/../../../resources/mapAssets/xml/'.$dosya;
        $tum = __DIR__ . '/../../151/' . $dosya;
        $xml = file_get_contents($tum);
        //$yeniArr = $this->gatewaysInfo();
        $yeniArr = "";
        //return view('m2boyut', ['xml' => $xml,'gwList' => $yeniArr]);

        //$devices = $this->Devices_model->get_all();
        //$devices = json_decode($client->get("devices"), true);
        $yeniArr = json_decode($client->get("gateways"), true);



        $data = array(
            'id' => '',
            'pageName' => "Map",
            'xml' => $xml,
            'gwList' => $yeniArr,
            //'devices' => $devices,
        );

        //echo json_encode($device_list);
        //die();
        $this->load->view('livemap', $data);
    }

    public function ermetal()
    {
        $client = $this->redis();

        $dosya = 'ermetal.xml';
        $tum = __DIR__ . '/../../ermetal/' . $dosya;
        $xml = file_get_contents($tum);
        $yeniArr = "";
        $yeniArr = json_decode($client->hgetall("F1"));



        $data = array(
            'id' => '',
            'pageName' => "Map",
            'xml' => $xml,
            'gwList' => $yeniArr,
            //'devices' => $devices,
        );

        //echo json_encode($device_list);
        //die();
        $this->load->view('ermetal', $data);
    }

    public function edit()
    {
        $client = $this->redis();

        $dosya = '151_0_p.xml';
        //$tum = __DIR__.'/../../../resources/mapAssets/xml/'.$dosya;
        $tum = __DIR__ . '/../../151/' . $dosya;
        $xml = file_get_contents($tum);
        //$yeniArr = $this->gatewaysInfo();
        $yeniArr = "";
        //return view('m2boyut', ['xml' => $xml,'gwList' => $yeniArr]);

        //$devices = $this->Devices_model->get_all();
        //$devices = json_decode($client->get("devices"), true);
        $yeniArr = json_decode($client->get("gateways"), true);

        $data = array(
            'id' => '',
            'pageName' => "Map",
            'xml' => $xml,
            'gwList' => $yeniArr,
            //'devices' => $devices,
        );

        $this->load->view('livemap_edit', $data);
    }

    public function get_ermetal_eski()
    {
        $device_list = [];

        $card_json = [];
        $client = $this->redis();
        $yeniArr = $client->hgetall("F1");


        $jyeniArr = array_keys($yeniArr, max($yeniArr));

        $becons['ac233fa0394c']['name'] = 'B1';
        $becons['ac233fa0394c']['lat'] = 40.919787;
        $becons['ac233fa0394c']['lng'] = 29.314991;

        $becons['ac233fa03964']['name'] = 'B2';
        $becons['ac233fa03964']['lat'] = 40.919755;
        $becons['ac233fa03964']['lng'] = 29.315066;

        $becons['ac233fa0394f']['name'] = 'B3';
        $becons['ac233fa0394f']['lat'] = 40.919803;
        $becons['ac233fa0394f']['lng'] = 29.31516;

        $becons['ac233fa0391c']['name'] = 'B4';
        $becons['ac233fa0391c']['lat'] = 40.919826;
        $becons['ac233fa0391c']['lng'] = 29.315214;

        $becons['ac233fa03970']['name'] = 'B5';
        $becons['ac233fa03970']['lat'] = 40.91985;
        $becons['ac233fa03970']['lng'] = 29.315251;

        $lat = $becons[$jyeniArr[0]]['lat'];
        $lng = $becons[$jyeniArr[0]]['lng'];
        $name = $becons[$jyeniArr[0]]['name'];

        $card_json['personName'] = 'F1';
        $card_json['gw_name'] = $name;
        $card_json['lat'] = (string) ($lat);
        $card_json['lng'] = (string) ($lng);

        array_push($device_list, $card_json);

        header('Content-Type: application/json');
        echo json_encode($device_list);
    }

    public function get_ermetal()
    {
        $device_list = [];

        $bjson = '{
            "ac233fa0394c": {
                "name": "B1",
                "lat": 40.919787,
                "lng": 29.314991
            },
            "ac233fa03964": {
                "name": "B2",
                "lat": 40.919755,
                "lng": 29.315066
            },
            "ac233fa0394f": {
                "name": "B3",
                "lat": 40.919803,
                "lng": 29.31516
            },
            "ac233fa0391c": {
                "name": "B4",
                "lat": 40.919826,
                "lng": 29.315214
            },
            "ac233fa03970": {
                "name": "B5",
                "lat": 40.91985,
                "lng": 29.315251
            }
        }';

        $client = $this->redis();

        $gwlists = '{
            "ac233fc00629": {
                "name": "Forklift 1"
            }
        }';

        $gwArray = json_decode($gwlists, true);

        foreach ($gwArray as $gwMac => $gwlist) {
            $gw_ts_lists = $client->hgetall($gwMac . "_ts");
            //$gw_rssi_lists = $client->hgetall($gwMac . "_rssi");

            $max_rssi_list = array();

            foreach ($gw_ts_lists as $key => $gw_ts_list) {

                $time_div = time() - $gw_ts_list;
                if ($time_div <= 5) { // 5 sn altındaki kayıtlar.
                    
                    $mac = $key;
                    $rssi_value = $client->hget($gwMac . "_rssi", $mac);
                    $max_rssi_list[$mac] = $rssi_value;

                    //echo $gw_ts_list." .. ".$time_div." .. ".$rssi_value."<br>  ";
                }
            }
            //echo time();

            arsort($max_rssi_list);
            $arr_count = count($max_rssi_list);

            $max_rssi = array_keys($max_rssi_list, max($max_rssi_list));
            $barray = json_decode($bjson, true);

            $card_json['personName'] = $gwArray[$gwMac]["name"];
            $card_json['gw_name'] = "";
            $card_json['lat'] = (string) ($barray[$max_rssi[0]]["lng"]);
            $card_json['lng'] = (string) ($barray[$max_rssi[0]]["lng"]);

            array_push($device_list, $card_json);
        }

        header('Content-Type: application/json');
        echo json_encode($device_list);
    }

    public function get_last_device_info()
    {
        $client = $this->redis();

        $device_list = [];

        $devices = json_decode($client->get("devices"), true);
        $personnel = json_decode($client->get("personnel"), true);
        $assets = json_decode($client->get("assets"), true);
        $gateways = json_decode($client->get("gateways"), true);


        $devices_keys = $client->keys("rtls:device:*");

        foreach ($devices_keys as $key => $devices_key) {
            $card_json = json_decode($client->get($devices_key));

            $card_device = json_decode($client->get($devices_key), true);

            foreach ($devices as $key => $device) {
                if ($device["mac"] == $card_device["mac"]) {
                    $device_id = $device["id"];
                    $person_name = $device["name"];
                }
            }
            foreach ($personnel as $key => $personn) {
                if ($personn["device_id"] == $device_id) {
                    $person_name = $personn["name"];
                }
            }
            foreach ($assets as $key => $asset) {
                if ($asset["device_id"] == $device_id) {
                    $person_name = $asset["name"];
                }
            }

            //try {
                foreach ($gateways as $key => $gateway) {
                    /*print_r($gateway);
                    echo " ";
                    echo " ";
                    echo " ";*/
                    if ($gateway["mac"] == $card_device["gateway"]) {
                        $lat = $gateway["lat"];
                        $lng = $gateway["lng"];
                        $gateway_name = $gateway["name"];
                    }
                }
            //} catch (\Throwable $th) {
                //throw $th;
            //}

            $card_json->personName = $person_name;
            $card_json->gw_name = $gateway_name;
            $card_json->lat = (string) ($lat + (@$card_device["rssi"] / 10000000));
            $card_json->lng = (string) ($lng + (@$card_device["rssi"] / 10000000));

            array_push($device_list, $card_json);
        }

        header('Content-Type: application/json');
        echo json_encode($device_list);
    }
}

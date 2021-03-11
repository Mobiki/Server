<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();


class Ermetal extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Devices_model");
        $this->load->model("Gateways_model");
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

        $dosya = 'ermetal.xml';
        $tum = __DIR__ . '/../../demo/' . $dosya;
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



    public function get_ermetal()
    {
        $device_list = [];

        $bjson = '{
            "ac233fa03964": {
                "name": "B1",
                "lat": 40.27102125713,
                "lng": 29.06173538282
            },
            "ac233fa0394f": {
                "name": "B2",
                "lat": 40.27095040744,
                "lng": 29.06203429278
            },
            "ac233fa0391c": {
                "name": "B3",
                "lat": 40.27083434984,
                "lng": 29.06221116205
            },
            "ac233fa03970": {
                "name": "B4",
                "lat": 40.27073448617,
                "lng": 29.06215191085
            },
            "ac233fa0394c": {
                "name": "B5",
                "lat": 40.27060155997,
                "lng": 29.06246762162
            },
            "d42202001469": {
                "name": "B6",
                "lat": 40.27066768606,
                "lng": 29.06231374535
            },
            "ac233fa03916": {
                "name": "B7",
                "lat": 40.27051923965,
                "lng": 29.0624729277
            },
            "ac233fa03c12": {
                "name": "B8",
                "lat": 40.27062450169,
                "lng": 29.06205197882
            }
        }';
//ac233fa039dd
        $client = $this->redis();

        $gwlists = '{
         "ac233fc00676":{
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
                }
            }

            arsort($max_rssi_list);
            $array_count = count($max_rssi_list);

            $max_rssi = array_keys($max_rssi_list, max($max_rssi_list));
            $barray = json_decode($bjson, true);

            $card_json['personName'] = $gwArray[$gwMac]["name"];
            $card_json['gw_name'] = "";
            $card_json['lat'] = (string) ($barray[$max_rssi[0]]["lat"]);
            $card_json['lng'] = (string) ($barray[$max_rssi[0]]["lng"]);

            array_push($device_list, $card_json);

            //$client->del($gwMac . "_rssi");
            //$client->del($gwMac . "_ts");
        }

        header('Content-Type: application/json');
        echo json_encode($device_list);


    }
}

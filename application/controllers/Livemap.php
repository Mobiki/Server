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
        $devices = json_decode($client->get("devices"), true);
        $yeniArr = json_decode($client->get("gateways"), true);

        $data = array(
            'id' => '',
            'pageName' => "Map",
            'xml' => $xml,
            'gwList' => $yeniArr,
            'devices' => $devices,
        );
        $this->load->view('livemap', $data);
    }

    public function getLastDeviceInfo()
    {
        $client = $this->redis();
        $device_mac = $this->input->get("mac");

        $card_device = json_decode($client->get("rtls:device:card:" . $device_mac), true);

        $devices = json_decode($client->get("devices"), true);
        $personnel = json_decode($client->get("personnel"), true);
        $gateways = json_decode($client->get("gateways"), true);

        foreach ($devices as $key => $device) {
            if ($device["mac"] == $device_mac) {
                $device_id = $device["id"];
            }
        }
        foreach ($personnel as $key => $personn) {
            if ($personn["device_id"] == $device_id) {
                $person_name = $personn["name"];
            }
        }

        foreach ($gateways as $key => $gateway) {
            if ($gateway["mac"] == $card_device["gateway"]) {
                $lat = $gateway["lat"];
                $lng = $gateway["lng"];
                $name = $gateway["name"];
            }
        }

        $card_device["location"] = (string) (@$lat + ($card_device["rssi"] / 10000000)) . "#" . (string) (@$lng + ($card_device["rssi"] / 10000000));
        $card_device["personName"] = $person_name;
        $card_device["gw_name"] = @$name;
        $card_device["lat"] = (string) (@$lat + ($card_device["rssi"] / 10000000));
        $card_device["lng"] = (string) (@$lng + ($card_device["rssi"] / 10000000));

        header('Content-Type: application/json');
        echo json_encode($card_device);
    }

    public function widget()
    {
        $dosya = '151_0_p.xml';

        //$tum = __DIR__.'/../../../resources/mapAssets/xml/'.$dosya;
        $tum = __DIR__ . '/../../151/' . $dosya;

        $xml = file_get_contents($tum);

        //$yeniArr = $this->gatewaysInfo();
        $yeniArr = "";

        //return view('m2boyut', ['xml' => $xml,'gwList' => $yeniArr]);
        $devices = $this->Devices_model->get_all();

        $data = array(
            'id' => '',
            'pageName' => "Map",
            'xml' => $xml,
            'gwList' => $yeniArr,
            'devices' => $devices,
        );
        $this->load->view('live_map_widget', $data);
    }
}

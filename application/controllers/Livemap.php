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

    public function index()
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
        $this->load->view('livemap', $data);
    }

    public function getLastDeviceInfo()
    {
        $devicemac = $this->input->get("mac");

        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_scheme'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_auth')
        ]);

        $date = [];

        $userget = json_decode($client->get("user:" . $devicemac), true);

        $gwinfo = $this->Gateways_model->get_by_mac($userget["gateway"]);

        $userget["location"] = (string) ($gwinfo[0]["lat"] + ($userget["rssi"] / 10000000)) . ", " . (string) ($gwinfo[0]["lng"] + ($userget["rssi"] / 10000000));
        $userget["personName"] = $devicemac;
        $userget["gw_name"] = $devicemac;

        header('Content-Type: application/json');
        echo json_encode($userget);
    }
}

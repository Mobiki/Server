<?php
require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();
class Tayfun extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function redis()
    {
        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_scheme'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_auth')
        ]);
        return $client;
    }

    public function sensor()
    {
        $devicemac = $this->input->get("mac");
        
        $client = $this->redis();
//ac233fa03af0
        $sensorinfo = json_decode($client->get("rtls:device:sensor:".$devicemac), true);
        header('Content-Type: application/json');
        echo json_encode($sensorinfo);
    }
}
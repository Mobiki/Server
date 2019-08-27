<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();


class Alert extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Alert_model");
        $this->load->model("Devices_model");

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

    public function index()
    {
        $devices=$this->Devices_model->get_all();
        $alert_rules=$this->Alert_model->getAllAlerts();
        $devices_type=$this->Devices_model->get_all_device_type();
        $data=array(
            'id'=>'',
            'page'=>'alert',
            'devices'=>$devices,
            'alert_rules'=>$alert_rules,
            'devices_type'=>$devices_type,
        );
        $this->load->view("alert",$data);
    }

    public function toredis()
    {
        $client = $this->redis();
        $alert_rules=$this->Alert_model->getAllAlerts();
        //print_r( json_decode(json_encode($gateways),true));
        $client->set("alert_rules",json_encode($alert_rules));
    }
}
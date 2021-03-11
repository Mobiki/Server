<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Gateways extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Gateways_model");
        $this->load->model("Zones_model");
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
        $gateways = $this->Gateways_model->get_all();
        $zones = $this->Zones_model->get_all();
        $gateway_type = $this->Gateways_model->get_all_type();

        $data = array(
            'pageId' => '',
            'pageName' => 'Gateways',
            'section' => 'all',
            'gateways' => $gateways,
            'zones' => $zones,
            'gateway_types' => $gateway_type,
        );
        $this->load->view('gateways', $data);
    }

    public function detail()
    {
        $mac = $this->input->get("mac");
        $gateway = $this->Gateways_model->get_by_mac($mac);
        $zones = $this->Zones_model->get_all();
        $data = array(
            'pageId' => '4.1',
            'section' => 'detail',
            'pageName' => 'Gateway Detail ',
            'gateway' => $gateway,
            'zones' => $zones,
        );
        $this->load->view('gateways', $data);
    }

    public function add()
    {
        $data = array(
            'name' => $this->input->post("name", true),
            'zone_id' => $this->input->post("zone_id", true),
            'type' => $this->input->post("gateway_type_id", true),
            'lat' => $this->input->post("lat", true),
            'lng' => $this->input->post("lng", true),
            'mac' => $this->input->post("mac", true),
            'description' => $this->input->post("description", true),
            'status' => $this->input->post("status", true),
        );

        $this->Gateways_model->insert($data);
        $this->toredis();
        redirect('gateways');
    }

    public function delete()
    {
        $id = $this->input->post("id", true);
        $this->Gateways_model->delete($id);
        $this->toredis();
        redirect('gateways');
    }


    public function edit()
    {
        $id = $this->input->post("id", true);
        $data = array(
            'name' => $this->input->post("name", true),
            'zone_id' => $this->input->post("zone_id", true),
            'type' => $this->input->post("gateway_type_id", true),
            'lat' => $this->input->post("lat", true),
            'lng' => $this->input->post("lng", true),
            'mac' => $this->input->post("mac", true),
            'description' => $this->input->post("description", true),
            'status' => $this->input->post("status", true),
        );
        $this->Gateways_model->update($id, $data);
        $this->toredis();
        redirect('gateways');
    }

    public function config()
    {
        //$this->load->library('phpMQTT');
die();
        require(APPPATH . 'libraries/phpMQTT.php');

        $server = '95.217.133.124';     // change if necessary
        $port = 1883;                     // change if necessary
        $username = 'mobiki';                   // set your username
        $password = '6nWYxC4N';                   // set your password
        $client_id = 'phpMQTT'; // make sure this is unique for connecting to sever - you could use uniqid()

        $mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
        echo("test");
        if (!$mqtt->connect(true, NULL, $username, $password)) {
            //exit(1);
            echo("error");
        }else{
            echo("ok");
        }

        //$mqtt->debug = true;

        //$topics['/gw/a4cf1231b62c/conf/resp'] = array('qos' => 0, 'function' => 'procMsg');
        //$mqtt->subscribe($topics, 0);

        //echo $mqtt->subscribeAndWaitForMessage('/gw/a4cf1231b62c/conf/resp', 0);

//$mqtt->close();

        $gw_mac = $this->input->get("mac");

        //echo $gw_mac;
        $data = array(
            'gw_mac' => $gw_mac,
        );
        $this->load->view('gateway_config', $data);
    }


    public function toredis()
    {
        $client = $this->redis();
        $gateways = $this->Gateways_model->get_all();
        $client->set("gateways", json_encode($gateways));
    }
}

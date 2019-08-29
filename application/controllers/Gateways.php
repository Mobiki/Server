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

        $data = array(
            'pageId' => '',
            'pageName' => 'Gateways',
            'section' => 'all',
            'gateways' => $gateways,
            'zones' => $zones,
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
            'lat' => $this->input->post("lat", true),
            'lng' => $this->input->post("lng", true),
            'mac' => $this->input->post("mac", true),
            'description' => $this->input->post("description", true),
        );
        
        $this->Gateways_model->insert($data);
        redirect('gateways');
    }

    public function delete()
    {
        $id = $this->input->post("hid", true);
        $this->Gateways_model->delete($id);
        redirect('gateways');
    }


    public function edit()
    {
        $id = $this->input->post("eid", true);
        $data = array(
            'name' => $this->input->post("ename", true),
            'zone_id' => $this->input->post("ezone_id", true),
            'lat' => $this->input->post("elat", true),
            'lng' => $this->input->post("elng", true),
            'mac' => $this->input->post("emac", true),
            'description' => $this->input->post("edescription", true),
        );

        $this->Gateways_model->update($id, $data);
        redirect('gateways');
    }


    public function toredis()
    {
        $client = $this->redis();
        $gateways = $this->Gateways_model->get_all();
        $client->set("gateways", json_encode($gateways));
    }
}

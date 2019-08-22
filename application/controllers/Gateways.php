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
            'password' => $this->config->item('redis_password')
        ]);
        return $client;
    }

    public function index()
    {
        $gateways = $this->Gateways_model->getAll();
        $zones = $this->Zones_model->getAllZones();

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
        $gwmac = $this->input->get("mac");

        $gateway = $this->Gateways_model->getDetail($gwmac);

        $data = array(
            'id' => '',
            'section' => 'detail',
            'gateway' => $gateway,
        );
        $this->load->view('gateways', $data);
    }

    public function add()
    {
        $name = $this->input->post("name", true);
        $zone_id = $this->input->post("zone_id", true);
        $lat = $this->input->post("lat", true);
        $lng = $this->input->post("lng", true);
        $mac = $this->input->post("mac", true);
        $description = $this->input->post("description", true);
        $data = array(
            'name' => $name,
            'zone_id' => $zone_id,
            'lat' => $lat,
            'lng' => $lng,
            'mac' => $mac,
            'description' => $description,
        );
        $this->db->insert('gateways', $data);
        redirect('gateways');
    }

    public function delete()
    {
        $mac = $this->input->post("hmac", true);
        $id = $this->input->post("hid", true);

        $this->db->where('id', $id);
        $this->db->where('mac', $mac);
        $this->db->delete('gateways');
        redirect('gateways');
    }


    public function edit()
    {
        $id = $this->input->post("eid", true);
        $name = $this->input->post("ename", true);
        $zone_id = $this->input->post("ezone_id", true);
        $lat = $this->input->post("elat", true);
        $lng = $this->input->post("elng", true);
        $mac = $this->input->post("emac", true);
        $description = $this->input->post("edescription", true);
        $data = array(
            'name' => $name,
            'zone_id' => $zone_id,
            'lat' => $lat,
            'lng' => $lng,
            'mac' => $mac,
            'description' => $description,
            'id'=>$id,
        );

        $this->db->where('id', $id);
        $this->db->update('gateways', $data);
        redirect('gateways');
    }


    public function toredis(Type $var = null)
    {
        $client = $this->redis();
        $gateways = $this->Gateways_model->getAll();
        $client->set("gateways",json_encode($gateways));
    }
}

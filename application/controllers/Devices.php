<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();


class Devices extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Devices_model");
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
        /*$client = $this->redis();
        $userkeys = $client->keys("user:*");*/

        $devices=$this->Devices_model->getAll();
        $devices_type=$this->Devices_model->getDevicesType();
        $data = array(
            'id' => '',
            'devices'=>$devices,
            'devices_type'=>$devices_type,
        );
        $this->load->view('devices', $data);
    }


    public function add(Type $var = null)
    {
        //`id``name``mac``description``type_id``status``update_date`
        $name = $this->input->post("name", true);
        $mac = $this->input->post("mac", true);
        $description = $this->input->post("description", true);
        $status = $this->input->post("hstatus", true);
        $type_id = $this->input->post("type_id", true);
        
        $data = array(
            'name' => $name,
            'type_id' => $type_id,
            'status' => $status,
            'mac' => $mac,
            'description' => $description,
        );

        $this->db->insert('devices', $data);
        redirect('devices');
    }

    public function edit(Type $var = null)
    {
        $id = $this->input->post("eid", true);
        $name = $this->input->post("ename", true);
        $type_id = $this->input->post("etype_id", true);
        $status = $this->input->post("hestatus", true);
        $mac = $this->input->post("emac", true);
        $description = $this->input->post("edescription", true);
        $data = array(
            'name' => $name,
            'type_id' => $type_id,
            'status' => $status,
            'mac' => $mac,
            'description' => $description,
            'id'=>$id,
        );


        $this->db->where('id', $id);
        $this->db->update('devices', $data);
        redirect('devices');
    }

    public function delete(Type $var = null)
    {
        $mac = $this->input->post("hmac", true);
        $id = $this->input->post("hid", true);

        $this->db->where('id', $id);
        $this->db->where('mac', $mac);
        $this->db->delete('devices');
        redirect('devices');
    }


    public function toredis(Type $var = null)
    {
        $client = $this->redis();
        $devices=$this->Devices_model->getAll();
        //print_r( json_decode(json_encode($gateways),true));
        $client->set("devices",json_encode($devices));
    }

}

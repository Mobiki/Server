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
            'password' => $this->config->item('redis_auth')
        ]);
        return $client;
    }

    public function index()
    {
        $devices = $this->Devices_model->get_all();
        $devices_type = $this->Devices_model->get_all_device_type();
        $data = array(
            'pageId' => '5',
            'pageName' => 'Devices',
            'devices' => $devices,
            'devices_type' => $devices_type,
        );
        $this->load->view('devices', $data);
    }


    public function add()
    {
        $data = array(
            'name' => $this->input->post("name", true),
            'type_id' => $this->input->post("type_id", true),
            'status' => $this->input->post("hstatus", true),
            'mac' => $this->input->post("mac", true),
            'description' => $this->input->post("description", true),
        );

        $this->Devices_model->insert($data);
        redirect('devices');
    }

    public function edit()
    {
        $id = $this->input->post("eid", true);

        $data = array(
            'name' => $this->input->post("ename", true),
            'type_id' => $this->input->post("etype_id", true),
            'status' => $this->input->post("hestatus", true),
            'mac' => $this->input->post("emac", true),
            'description' => $this->input->post("edescription", true),
        );

        $result = $this->Devices_model->update($id, $data);
        
        if ($result) {
            redirect('devices');
        } else {
            echo "Error - Devices - update";
        }
    }

    public function delete()
    {
        $id = $this->input->post("hid", true);

        $result = $this->Devices_model->delete($id);

        if ($result) {
            redirect('devices');
        } else {
            echo "Error - Devices - delete";
        }
    }


    public function toredis()
    {
        $client = $this->redis();
        $devices = $this->Devices_model->get_all();
        $devices_type = $this->Devices_model->get_all_device_type();
        $client->set("devices", json_encode($devices));
        $client->set("device_type", json_encode($devices_type));
    }

    public function add_device_type()
    {
        $data = array(
            'name' => $this->input->post("name", true),
            'description' => $this->input->post("description", true),
        );

        $result = $this->Devices_model->insert_device_type($data);

        if ($result) {
            redirect('devices');
        } else {
            echo "Error - Devices - insert_device_type";
        }
    }
}

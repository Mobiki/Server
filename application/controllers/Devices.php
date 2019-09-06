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
            'status' => $this->input->post("status", true),
            'mac' => $this->input->post("mac", true),
            'description' => $this->input->post("description", true),
        );

        $result = $this->Devices_model->insert($data);

        if ($result) {
            $this->toredis();
            redirect('devices');
        } else {
            echo "Error - Devices - add";
        }
    }

    public function edit()
    {
        $id = $this->input->post("id", true);

        $data = array(
            'name' => $this->input->post("name", true),
            'type_id' => $this->input->post("type_id", true),
            'status' => $this->input->post("status", true),
            'mac' => $this->input->post("mac", true),
            'description' => $this->input->post("description", true),
        );

        $result = $this->Devices_model->update($id, $data);

        if ($result) {
            $this->toredis();
            redirect('devices');
        } else {
            echo "Error - Devices - update";
        }
    }

    public function delete()
    {
        $id = $this->input->post("id", true);

        $result = $this->Devices_model->delete($id);

        if ($result) {
            $this->toredis();
            redirect('devices');
        } else {
            echo "Error - Devices - delete";
        }
    }

    public function device_types_index()
    {
        $device_type = $this->Devices_model->get_all_device_type();

        $data = array(
            'device_type' => $device_type,
        );
        $this->load->view('device_types', $data);
    }

    public function add_device_type()
    {
        $data = array(
            'name' => $this->input->post("name", true),
            'description' => $this->input->post("description", true),
        );

        $result = $this->Devices_model->insert_device_type($data);

        if ($result) {
            $this->toredis();
            redirect('devices');
        } else {
            echo "Error - Devices - insert_device_type";
        }
    }

    public function edit_device_type()
    {
        $id = $this->input->post("id", true);
        $data = array(
            'name' => $this->input->post("name", true),
            'description' => $this->input->post("description", true),
        );

        $result = $this->Devices_model->update_device_type($id, $data);

        if ($result) {
            $this->toredis();
            redirect('devices');
        } else {
            echo "Error - Devices - update_device_type";
        }
    }

    public function delete_device_type()
    {
        $id = $this->input->post("id", true);
        $result = $this->Devices_model->delete_device_type($id);

        if ($result) {
            $this->toredis();
            redirect('devices');
        } else {
            echo "Error - Devices - delete_device_type";
        }
    }

    public function toredis()
    {
        $client = $this->redis();
        $devices = $this->Devices_model->get_all();
        $client->set("devices", json_encode($devices));

        $devices_type = $this->Devices_model->get_all_device_type();
        $client->set("device_type", json_encode($devices_type));
    }
}

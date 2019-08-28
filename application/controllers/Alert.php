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
        $devices = $this->Devices_model->get_all();
        $alert_rules = $this->Alert_model->get_all_alert_rules();
        $devices_type = $this->Devices_model->get_all_device_type();

        $data = array(
            'pageId' => '10',
            'pageName' => 'Alerts',
            'devices' => $devices,
            'alert_rules' => $alert_rules,
            'devices_type' => $devices_type,
        );
        $this->load->view("alert", $data);
    }

    public function add()
    {
        $data = array(
            'name' => $this->input->post("name"),
            'device_id' => $this->input->post("device_id"),
            'device_type' => $this->input->post("device_type"),
            'sensor_value' => $this->input->post("sensor_value"),
            'equation' => $this->input->post("equation"),
        );

        $result = $this->Alert_model->insert_alert_rule($data);

        if ($result) {
            $this->toredis();//Send to redis
            redirect('alert');
        } else {
            echo "Error - Alert - insert";
        }
    }

    public function edit()
    {
        $id = $this->input->post("id",true);

        $data = array(
            'name' => $this->input->post("name",true),
            'device_id' => $this->input->post("device_id",true),
            'device_type' => $this->input->post("device_type",true),
            'sensor_value' => $this->input->post("sensor_value",true),
            'equation' => $this->input->post("equation",true),
        );

        $result = $this->Alert_model->update_alert_rule($id,$data);

        if ($result) {
            $this->toredis();//Send to redis
            redirect('alert');
        } else {
            echo "Error - Alert - Update";
        }
    }

    public function delete()
    {
        $id = $this->input->post("id",true);

        $result = $this->Alert_model->delete_alert_rule($id);

        if ($result) {
            $this->toredis();//Send to redis
            redirect('alert');
        } else {
            echo "Error - Alert - Delete";
        }
    }

    public function toredis()
    {
        $client = $this->redis();
        $alert_rules = $this->Alert_model->get_all_alert_rules();
        $client->set("alert_rules", json_encode($alert_rules));
    }
}

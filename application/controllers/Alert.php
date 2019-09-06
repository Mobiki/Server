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
        $this->load->model("Users_model");
        $this->load->model("Gateways_model");
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
            $this->toredis(); //Send to redis
            redirect('alert');
        } else {
            echo "Error - Alert - insert";
        }
    }

    public function edit()
    {
        $id = $this->input->post("id", true);

        $data = array(
            'name' => $this->input->post("name", true),
            'device_id' => $this->input->post("device_id", true),
            'device_type' => $this->input->post("device_type", true),
            'sensor_value' => $this->input->post("sensor_value", true),
            'equation' => $this->input->post("equation", true),
        );

        $result = $this->Alert_model->update_alert_rule($id, $data);

        if ($result) {
            $this->toredis(); //Send to redis
            redirect('alert');
        } else {
            echo "Error - Alert - Update";
        }
    }

    public function delete()
    {
        $id = $this->input->post("id", true);

        $result = $this->Alert_model->delete_alert_rule($id);

        if ($result) {
            $this->toredis(); //Send to redis
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


    public function logs()
    {
        $devices = $this->Devices_model->get_all();
        $users = $this->Users_model->get_all();
        $alert_logs = $this->Alert_model->get_all_alert_logs();
        $alert_rules = $this->Alert_model->get_all_alert_rules();
        $gateweys = $this->Gateways_model->get_all();
        $data = array(
            'pageId' => '10.1',
            'pageName' => 'Alert Logs',
            'devices' => $devices,
            'users' => $users,
            'alert_logs' => $alert_logs,
            'gateweys' => $gateweys,
            'alert_rules' => $alert_rules,
        );
        $this->load->view("alert_logs", $data);
    }








    public function suspend_alert()
    {
        $client = $this->redis();

        $user_data = $this->session->userdata('userdata');

        $alert_key = $this->input->get("alert_key");

        if ($user_data["id"] != 0 && $user_data["id"] != "") {
            $data = array(
                'alert_rules_id' => $this->input->get("alert_rules_id"),
                'device_id' => $this->input->get("device_id"),
                'gateway_id' => $this->input->get("gateway_id"),
                'suspended_user_id' => $user_data["id"],
                'suspend_date' => date('Y-m-d H:i:s'),
                'status' => 2,
            );

            $this->Alert_model->insert_alert_log($data);

            $lightinfo = json_decode($client->get($alert_key), true);
            $lightinfo["status"] = 2;
            $lightsuspend = json_encode($lightinfo);
            $client->set($alert_key, $lightsuspend);
        }
    }

    public function close_alert()
    {
        $client = $this->redis();
        $alert_log_id = $this->input->get("alert_log_id");
        $alert_key = $this->input->get("alert_key");

        $user_data = $this->session->userdata('userdata');
        //if ($user_data["id"] != 0 && $user_data["id"] != "") {
            $data = array(
                'closed_user_id' => $user_data["id"],
                'close_date' => date('Y-m-d H:i:s'),
                'status' => 3,
            );
            $this->Alert_model->alert_close($alert_log_id, $data);
            $client->del($alert_key);
        //}
    }
}

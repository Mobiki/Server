<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Logs extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Personnel_model");
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

    public function personnel()
    {
        $personnel = $this->Personnel_model->get_all();
        $data = array(
            'personnel' => $personnel,
            //'pageId' => '3',
            'pageName' => 'Personnel Logs',
            //'zones' => $zones,
        );
        $this->load->view('personnel_logs', $data);
    }

    public function assets()
    {
        $this->load->view('assets_logs');
    }

    public function gateway()
    {
        $this->load->view('gateway_logs');
    }

    public function device()
    {
        $devices = $this->Devices_model->get_all();
        $data = array(
            //'personnel' => $personnel,
            //'pageId' => '3',
            'pageName' => 'Device Logs',
            'devices' => $devices,
        );
        $this->load->view('device_logs', $data);
    }

    public function get_personal_logs()
    {
        $sDate = $this->input->post("sDate");
        $fDate = $this->input->post("fDate");
        $person_id = $this->input->post("personnel_id");

        $client = $this->redis();
        $personnel = json_decode($client->get("personnel"), true);
        $devices = json_decode($client->get("devices"), true);
        $gateways = json_decode($client->get("gateways"), true);

        foreach ($personnel as $key => $person) {
            if ($person["id"] == $person_id) {
                $device_id = $person["device_id"];
            }
        }

        foreach ($devices as $key => $device) {
            if ($device["id"] == $device_id) {
                $device_mac = $device["mac"];
            }
        }

        $data_list = [];
        $datas = $client->zrangebyscore("log:device:card:" . $device_mac, $sDate, $fDate);
        //$datas = $client->zrange("log:device:card:" . $device_mac, 0, -1);
        header('Content-Type: application/json');
        //$logs = preg_replace('/\\\"/',"\"", $logs);

        //$adata = [];
        date_default_timezone_set('Europe/Istanbul');
        foreach ($datas as $key => $data) {
            //print_r($key);
            $djson = json_decode($data, true);
            $d=[];
            $gateway_name = "";
            foreach ($gateways as $key => $gateway) {
                
                if ($gateway["mac"] == $djson["gateway"]) {
                    $gateway_name = $gateway["name"];
                    //echo $gateway_name;
                }
            }//print_r($gateway_name);
            $date = date("d/m/Y H:i:s", $djson["epoch"]);

            $d["ts"] = $djson["epoch"];
            $d["date_time"] = $date;
            $d["gateway_name"] = $gateway_name;
            if ($gateway_name != "") {
                $status = "In";
            } else {
                $status = "Out";
            }

            $d["status"] = $status;
            array_push($data_list, $d);


            //$adata[$key]["date"] = date("d/m/Y H:i:s", $djson["epoch"]);
            //$adata[$key]["gateway_name"] = $gateway_name;
        }

        $result["data"] = $data_list;
        echo json_encode($result);

        //echo '{"data": [{"date": "1","zone": "Tiger Nixon"}]}';
    }
}

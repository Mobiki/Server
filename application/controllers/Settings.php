<?php



class Settings extends CI_Controller
{
    
    function __construct()
    {
        parent::__construct();
        $this->load->library('redis');
    }

    public function index()
    {
        $data = array(
            'pageId' => 12,
            'pageName' => "Settings",
        );
        $this->load->view('settings', $data);
    }


    public function redisSetting()
    {

        if ($this->input->post("redis_status") != "") {
            $redis_status = "TRUE";
        } else {
            $redis_status = "FALSE";
        }
        $redis_scheme = $this->input->post("redis_scheme", true);
        $redis_host = $this->input->post("redis_host", true);
        $redis_port = $this->input->post("redis_port", true);
        $redis_auth = $this->input->post("redis_auth", true);

        $filename = "redis.php";
        $ourFileHandle = fopen(APPPATH . '/config/' . $filename, 'w');
        $written = '<?php
defined("BASEPATH") OR exit("No direct script access allowed");

//Redis Config
$config["redis_status"] = ' . $redis_status . ';
$config["redis_scheme"] = "' . $redis_scheme . '";
$config["redis_host"] = "' . $redis_host . '";
$config["redis_port"] = ' . $redis_port . ';
$config["redis_auth"] = "' . $redis_auth . '";';

        fwrite($ourFileHandle, $written); //write new db connect file
        fclose($ourFileHandle); //file close

        redirect("settings");
    }

    public function redisTest()
    {
        
        try {
            $client = new Predis\Client([
                'scheme' => $this->config->item('redis_scheme'),
                'host'   => $this->config->item('redis_host'),
                'port'   => $this->config->item('redis_port'),
                'password' => $this->config->item('redis_auth')
            ]);

            $client->set('testkey', 'testvalue');
            if ($client->get('testkey') == "testvalue") {
                echo 'Connection successful';
                $client->del('testkey');
                return true;
            } else {
                echo 'Connection Failed';
                return false;
            }
        } catch (Exception $e) {
            echo 'Connection Failed';
            return false;
        }
    }
}

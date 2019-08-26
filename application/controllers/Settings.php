<?php

class Settings extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
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
        $redis_status = $this->input->post("redis_status");
        $redis_scheme = $this->input->post("redis_scheme");
        $redis_host = $this->input->post("redis_host");
        $redis_port = $this->input->post("redis_port");
        $redis_password = $this->input->post("redis_password");

        $filename = "redis.php";
        $ourFileHandle = fopen(APPPATH . '/config/' . $filename, 'w');
        $written = '<?php
defined("BASEPATH") OR exit("No direct script access allowed");

//Redis Config
$config["redis_status"] = ' . $redis_status . ';
$config["redis_scheme"] = "' . $redis_scheme . '";
$config["redis_host"] = "' . $redis_host . '";
$config["redis_port"] = ' . $redis_port . ';
$config["redis_password"] = "' . $redis_password . '";';

        fwrite($ourFileHandle, $written); //write new db connect file
        fclose($ourFileHandle); //file close

        redirect("settings");
    }
}

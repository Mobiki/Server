<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Personnel extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->load->model("Personnel_model");
        $this->load->model("Devices_model");
    }


    public function index()
    {
        $devices = $this->Devices_model->get_all();
        $devices_type = $this->Devices_model->get_all_device_type();
        $data = array(
            'pageId' => '7',
            'pageName' => 'Personnel',
            'devices' => $devices,
            'devices_type' => $devices_type,
        );
        $this->load->view('personnel', $data);
    }

    public function delete()
    {
        # code...
    }

    public function edit()
    {
        # code...
    }

    public function assigneDevice()
    {
        # code...
    }

    public function toredis()
    {
        # code...
    }
}

<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();


class Zones extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Zones_model");
    }

    public function index(Type $var = null)
    {
        $zones= $this->Zones_model->getAllZones();
        $data=array(
            'pageId'=>'3',
            'pageName'=>'Zones',
            'zones'=>$zones,
        );
        $this->load->view("zones",$data);
    }

}
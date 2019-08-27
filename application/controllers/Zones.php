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

    public function add()
    {
        $name = $this->input->post("name", true);
        $parent_id = $this->input->post("parent_id", true);
        $description = $this->input->post("description", true);

        $zones = $this->Zones_model->addzone($name,$parent_id,$description);
        if ($zones) {
            $this->toredis();
            redirect('zones');
        } else {
            # code...
        }
        
        
    }

    public function delete()
    {
        # code...
    }

    public function edit()
    {
        # code...
    }



    public function toredis()
    {
        $client = $this->redis();
        $zones=$this->Zones_model->getAllZones();
        //print_r( json_decode(json_encode($gateways),true));
        $client->set("zones",json_encode($zones));
    }

}
<?php
require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class History extends CI_Controller
{

    
    function __construct()
    {
        parent::__construct();
        //$this->load->model("History_model");
    }


    public function index()
    {


        $data = array(
            'id' => '',
        );
        $this->load->view('history', $data);
    }


    public function userlog()
    {
        $getmac=$this->input->get("mac");

        
        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_host'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_auth')
        ]);

        $userlog = $client->zrange("log:".$getmac,0,-1,array('withscores' => true));
        //header('Content-Type: application/json');

        $data=array(
            'mac'=>$getmac,
            'userlog'=>$userlog,

        );
        $this->load->view('history',$data);
        //echo json_encode($userlog);
    }

    public function userlogjson()
    {
        $getmac=$this->input->get("mac");

        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_scheme'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_auth')
        ]);

        $userlog = $client->zrange("log:".$getmac,0,-1,array('withscores' => true));
        //header('Content-Type: application/json');

        $data=array(
            'mac'=>$getmac,
            'userlog'=>$userlog,

        );
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

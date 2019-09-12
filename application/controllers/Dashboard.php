<?php
date_default_timezone_set('Europe/Istanbul');
defined('BASEPATH') or exit('No direct script access allowed');


require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();



class Dashboard extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function redis()
    {
        if ($this->config->item('redis_status') == TRUE) {
            $client = new Predis\Client([
                'scheme' => $this->config->item('redis_scheme'),
                'host'   => $this->config->item('redis_host'),
                'port'   => $this->config->item('redis_port'),
                'password' => $this->config->item('redis_auth')
            ]);
            return $client;
        }
    }

    public function index()
    {
        $data = array(
            'pageId'    =>  1,
            'pageName' => 'Dashboard',
        );

        $this->load->view('dashboard', $data);
    }
}

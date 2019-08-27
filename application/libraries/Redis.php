<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Redis{

    public function __construct()
	{


    }
    public function connect()
    {
        $this->config->load('redis', TRUE);
        echo $this->config->item('redis_scheme','redis');
        die();
        
        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_scheme'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_auth')
        ]);
        return $client;
    }
}
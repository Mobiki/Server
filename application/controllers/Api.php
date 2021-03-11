<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

use Firebase\JWT\JWT;

class Api extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Alert_model");
        $this->load->model("Devices_model");
        $this->load->model("Users_model");
        $this->load->model("Gateways_model");
        $this->load->model("Personnel_model");
        $this->load->model("Assets_model");
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
    }

    public function login()
    {
        $error = array(
            'error' => 'invalid_grant',
            'error_description' => 'Invalid email or password combination.',
        );

        //$email = $this->input->post('email', true);
        //$password = $this->input->post('password', true);

        header('Content-Type: application/json');

        //$result = array();

        $email = $this->input->post('email', true);
		$password = $this->input->post('password', true);
        $result = $this->Users_model->login_check($email, $password);
        //echo json_encode($result);
        if ($result['auth'] == 'auth1') {

            //$this->session->set_userdata('userdata', $result);
            //$this->session->set_flashdata('login', 'true'); //giriş için tek kullanımlık.
            //redirect(site_url() . 'dashboard');
            echo json_encode($result);
        } else {
            //$this->session->sess_destroy();
            //$this->session->set_flashdata('login', 'false');
            //redirect(site_url() . 'login');
            echo json_encode($error);
        }
    }
}

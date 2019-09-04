<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Departments extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Departments_model");
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
        $departments = $this->Departments_model->get_all();

        $data = array(
            'pageId' => 8,
            'pageName' => "Departments",
            'departments' => $departments,
        );
        $this->load->view('departments', $data);
    }

    public function add()
    {
        if ($this->input->post('expiry_date', true)=="") {
            $expiry_date = "0000-00-00";
        } else {
            $expiry_date = $this->input->post('expiry_date', true);
        }
        
        $data = array(
            'name' => $this->input->post('name', true),
            'parent_id' => $this->input->post('parent_id', true),
            'expiry_date' => $expiry_date,
        );
        print_r($data);
        $result = $this->Departments_model->insert($data);
        if ($result) {
            redirect('departments');
        } else {
            echo "Error - Departments - Add";
        }
    }

    public function edit()
    { 
        $id = $this->input->post('id', true);
        $data = array(
            'name' => $this->input->post('name', true),
            'parent_id' => $this->input->post('parent_id', true),
            'expiry_date' => $this->input->post('expiry_date', true),
        );
        $result = $this->Departments_model->update($id,$data);
        if ($result) {
            redirect('departments');
        } else {
            echo "Error - Departments - Edit";
        }
    }

    public function delete()
    {
        $id = $this->input->post('id', true);
        $result = $this->Departments_model->delete($id);
        if ($result) {
            redirect('departments');
        } else {
            echo "Error - Departments - Delete";
        }
    }
}

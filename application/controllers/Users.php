<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Users_model");
    }

    public function index()
    {
        $users_data = $this->Users_model->get_all();
        $data = array(
            'pageId' => '11',
            'pageName' => 'Users',
            'users_data'    =>  $users_data,
        );

        $this->load->view('users', $data);
    }

    public function get_all_json()
    {
        $users_data = $this->Users_model->get_all();
        header('Content-Type: application/json');
        $data = array(
            'data' => $users_data,
        );

        echo json_encode($data);
    }

    public function edit()
    {
        $id =  $this->input->post('id', true);

        $data = array(
            'name' => $this->input->post('name', true),
            'email' => $this->input->post('email', true),
            'phone' => $this->input->post('phone', true),
            'description' => $this->input->post('description', true),
        );

        $result = $this->Users_model->update($id, $data);
        if ($result == true) {
            redirect('users');
        } else {
            echo "Error - Users - Update";
        }
    }

    public function delete()
    {
        $id =  $this->input->post('id', true);

        $result = $this->Users_model->delete($id);
        if ($result == true) {
            redirect('users');
        } else {
            //redirect('users');
        }
    }

    public function add()
    {
        $data = array(
            'name' => $this->input->post('name', true),
            'email' => $this->input->post('email', true),
            'password' => $this->input->post('password', true),
            'phone' => $this->input->post('phone', true),
            'description' => $this->input->post('description', true),
            'token' => "",
        );

        print_r($data);
        $result = $this->Users_model->insert($data);

        if ($result == true) {
            redirect('users');
        } else {
            echo "Error - Users - Add";
        }
    }

    public function cpass()
    {
        $id = $this->input->post('id', true);
        $password = $this->input->post('password', true);
        $result = $this->Users_model->change_password($id, $password);
        if ($result == true) {
            redirect('users');
        } else {
            echo "Error - Users - change_password";
        }
    }
}

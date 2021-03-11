<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Users_model");
        //header('Content-Type: application/json');

        $user_data = $this->session->userdata('userdata');


if ($user_data["role_id"]==2) {
    redirect('dashboard');
}
    }

    public function index()
    {
        $users_data = $this->Users_model->get_all();
        $users_role = $this->Users_model->get_all_users_role();
        $data = array(
            'pageId' => '11',
            'pageName' => 'Users',
            'users_data'    =>  $users_data,
            'users_role'    =>  $users_role,
        );
        $this->load->view('users', $data);
    }

    public function edit()
    {
        $id =  $this->input->post('id', true);

        $data = array(
            'name' => $this->input->post('name', true),
            'role_id' => $this->input->post('role_id', true),
            'email' => $this->input->post('email', true),
            'phone' => $this->input->post('phone', true),
            'description' => $this->input->post('description', true),
        );

        $result = $this->Users_model->update($id, $data);
        
        if ($result) {
            redirect('users');
        } else {
            redirect('users?error=db');
        }
        
    }

    public function delete()
    {
        //user_role==1 if
        $id =  $this->input->post('id', true);
        $result = $this->Users_model->delete($id);
        if ($result == true) {
            redirect('users');
        }
    }

    public function add()
    {
        $data = array(
            'name' => $this->input->post('name', true),
            'role_id' => $this->input->post('role_id', true),
            'email' => $this->input->post('email', true),
            'password' => $this->input->post('password', true),
            'phone' => $this->input->post('phone', true),
            'description' => $this->input->post('description', true),
            'token' => md5(""),
        );

        $result = $this->Users_model->insert($data);
        if ($result == true) {
            redirect('users');
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

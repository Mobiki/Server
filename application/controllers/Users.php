<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("users_model");
    }

    public function index()
    {
        $users_data = $this->users_model->users_get_all();
        $data = array(
            'users_data'    =>  $users_data,
        );

        $this->load->view('users', $data);
    }

    public function getAllJson(Type $var = null)
    {
        $users_data = $this->users_model->users_get_all();
        header('Content-Type: application/json');
        $data=array(
            'data'=> $users_data,
        );
        
        echo json_encode( $data);
    }

    public function get($id)
    {//id ye göre kullanıcı bilgileri getirme kontrol //edit için
        $users_data = $this->users_model->users_get($id);
        $data = array(
            'users_data'    =>  $users_data,
        );
        $this->load->view('usersget', $data);
    }

    public function edit($user_id)
    {//şifre hariç kullanıcı bilgileri değiştirme kontrol
        $name = $this->input->post('name', true);
        $role_id = $this->input->post('role_id', true);
        $email = $this->input->post('email', true);
        $phone = $this->input->post('phone', true);
        $description = $this->input->post('description', true);
        $token = $this->input->post('token', true);
        $result = $this->users_model->users_update($user_id,$name,$role_id,$email,$phone,$description,$token);
        if ($result == true) {
            redirect('users');
        } else {
            //redirect('users');
        }
    }

    public function delete($user_id)
    {//kullanıcı silme kontrol
        $result = $this->users_model->users_delete($user_id);
        if ($result == true) {
            redirect('users');
        } else {
            //redirect('users');
        }
    }

    public function add()
    {//yeni kullanıcı kontrol
        $name = $this->input->post('name', true);
        $role_id = $this->input->post('role_id', true);
        $email = $this->input->post('email', true);
        $password = $this->input->post('password', true);
        $phone = $this->input->post('phone', true);
        $description = $this->input->post('description', true);
        $token = $this->input->post('token', true);
        $result = $this->users_model->users_add($name, $role_id, $email, $password, $phone, $description, $token);
        if ($result == true) {
            redirect('users');
        } else {
            //redirect('users');
        }

    }

    public function editpassword($user_id)
    {//kullanıcın şifresi değiştirildiği kontrol 
        $password = $this->input->post('password', true);
        $result = $this->users_model->users_change_password($user_id,$password);
        if ($result == true) {
            redirect('users');
        } else {
            //redirect('users');
        }
    }

}

<?php

class Users_model extends CI_Model
{
    protected $table = 'users';
    protected $md5key = '4671dfb2178c8f4b231f94a2e1ae675e'; //önemli

    function __construct()
    {
        parent::__construct();
    }

    //users `id``role_id``name``email``password``phone``description``token`

    public function get_all()
    {
        return $this->db->get($this->table)->result_array();
    }

    public function users_count()
    {
        $query = $this->db->query('SELECT count(id) as users_count FROM users')->row_array();
        return $query['users_count'];
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->result_array();
    }

    public function change_password($id, $password)
    {
        $this->db->set('password', md5($password . md5($this->md5key))); //önemli
        $this->db->where('id', $id);
        return $this->db->update($this->table);
    }

    public function change_token($id, $token)
    {
        $this->db->set('token', $token);
        $this->db->where('id', $id);
        return $this->db->update($this->table);
    }

    public function login_check($email, $password)
    {
        $this->db->where('email', $email);
        $this->db->where('password', md5($password . md5($this->md5key))); //önemli
        $query = $this->db->get($this->table);

        if ($query->num_rows() == 1) {
            $query = $query->row_array();
            $data = array(
                'id'        => $query['id'],
                'role_id'   => $query['role_id'],
                'name'      => $query['name'],
                'email'     => $query['email'],
                'phone'     => $query['phone'],
                'description' => $query['description'],
                'token'     => $query['token'],
                'auth'      => 'auth1',
            );
            return $data;
        } else {
            $emptydata = array(
                'id'        => "",
                'role_id'   => "",
                'name'      => "",
                'email'     => "",
                'phone'     => "",
                'description' => "",
                'token'     => "",
                'auth'      => 'auth0',
            );
            return $emptydata;
        }
    }

    public function role_check($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row()->role_id;
    }

    public function insert($data)
    {
        $data['password'] = md5($data['password'] . md5($this->md5key));
        return $this->db->insert($this->table, $data);
    }
}

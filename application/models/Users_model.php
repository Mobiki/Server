<?php

class Users_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public $tableName = "users";
    //users `id``role_id``name``email``password``phone``description``token`

    public function users_get_all()
    { //tüm kullanıcıların getirildiği kısım
        return $this->db->get($this->tableName)->result_array();  //result_array()
    }

    public function users_count()
    {//toplam kullanıcı sayısı
        $query = $this->db->query('SELECT count(id) as users_count FROM users')->row_array();

        return $query['users_count'];
    }

    public function users_delete($user_id)
    {//kullanıcı silme
        $this->db->where('id', $user_id);
        $this->db->delete($this->tableName);

        return true;
    }

    public function users_update($user_id, $name, $role_id, $email, $phone, $description, $token)
    { //Şifre hariç kullanıcının güncellendiği kısım
        $this->db->set('name', $name);
        $this->db->set('role_id', $role_id);
        $this->db->set('email', $email);
        $this->db->set('phone', $phone);
        $this->db->set('description', $description);
        $this->db->set('token', $token);
        $this->db->where('id', $user_id);
        $this->db->update('users');

        return true;
    }

    public function users_get($id)
    { //id ye göre kullanıcı bilgilerinin getirildiği kısım
        $this->db->where('id', $id);
        return $this->db->get($this->tableName)->result_array();
    }

    public function users_buildings()
    { //kullanıcının binalarının getirildiği kısım // değiştirilecek
        $query = 'SELECT 
        users_buildings.user_id, 
        buildings.name as buildings_name, 
        buildings.id as buildings_id 
        FROM users_buildings 
        INNER JOIN buildings 
        ON users_buildings.building_id = buildings.id';

        return $this->db->query($query)->result_array();
    }

    public function users_change_password($user_id, $password)
    { //kullanıcının şifresinin değiştiği kısım
        $this->db->set('password', md5($password . md5('4671dfb2178c8f4b231f94a2e1ae675e'))); //önemli
        $this->db->where('id', $user_id);
        $this->db->update('users');

        return true;
    }

    public function users_change_token($user_id)
    {
        //-
    }

    public function users_login_check($email, $password)
    { //login kontrolü yapıldığı kısım
        $this->db->where('email', $email);
        $this->db->where('password', md5($password . md5('4671dfb2178c8f4b231f94a2e1ae675e'))); //önemli
        $query = $this->db->get($this->tableName);

        if ($query->num_rows() ==1) {
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

    public function users_role_check($user_id)
    {
        //-
    }

    public function users_add($name, $role_id, $email, $password, $phone, $description, $token)
    { //yeni kullanıcı eklendiği kısım
        $data = array(
            'name' => $name,
            'role_id' => $role_id,
            'email' => $email,
            'password' => md5($password . md5('4671dfb2178c8f4b231f94a2e1ae675e')), //önemli
            'phone' => $phone,
            'description' => $description,
            'token' => $token
        );
        try {
            $this->db->insert('users', $data);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
